<?php

namespace Accurateweb\MoyskladIntegrationBundle\Service;

use AccurateCommerce\Component\CdekShipping\Shipping\Method\ShippingMethodCdekTerminal;
use AccurateCommerce\Shipping\Method\Store\ShippingMethodStoreCourier;
use AccurateCommerce\Shipping\Method\Store\ShippingMethodStorePickup;
use Accurateweb\MoyskladIntegrationBundle\Event\MoyskladOrderCreateEvent;
use Accurateweb\MoyskladIntegrationBundle\Exception\MoyskladException;
use Accurateweb\MoyskladIntegrationBundle\Exception\MoyskladExceptionFactory;
use Accurateweb\MoyskladIntegrationBundle\Model\MoyskladManager;
use Accurateweb\MoyskladIntegrationBundle\Model\OrderItemTransformer;
use Accurateweb\SettingBundle\Model\Manager\SettingManagerInterface;
use StoreBundle\Entity\Store\Logistics\Delivery\Cdek\CdekRawPvzlist;
use StoreBundle\Entity\Store\Order\Order;
use StoreBundle\Entity\Store\Order\OrderItem;
use StoreBundle\Repository\Store\Logistics\Delivery\Cdek\CdekRawPvzlistRepository;
use Doctrine\Common\Persistence\ObjectManager;
use MoySklad\Components\FilterQuery;
use MoySklad\Entities\Counterparty;
use MoySklad\Entities\Documents\Orders\CustomerOrder;
use MoySklad\Entities\Misc\Attribute;
use MoySklad\Entities\Misc\CustomEntity;
use MoySklad\Exceptions\ApiResponseException;
use MoySklad\Exceptions\RequestFailedException;
use MoySklad\Lists\EntityList;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class MoyskladOrderSender
{
  # это всё пока не надо
/*  const ATTRIBUTE_ORDER_NUM = 'ac2633f9-6f98-11e8-9109-f8fc00013baa'; //Номер заказа ИМ string
  const ATTRIBUTE_CDEK_ORDER_NUM = 'ac263d50-6f98-11e8-9109-f8fc00013bab'; //Номер заказа СДЭК string
  const ATTRIBUTE_CDEK_ORDER_STATUS = 'ac266038-6f98-11e8-9109-f8fc00013bb4'; //Статус СДЭК string
  const ATTRIBUTE_RECIEVER = 'ac264708-6f98-11e8-9109-f8fc00013bad'; //Получатель string
  const ATTRIBUTE_RECIEVER_PHONE = 'ac264f02-6f98-11e8-9109-f8fc00013baf'; //Телефон получателя string
  const ATTRIBUTE_CITY = 'ac265908-6f98-11e8-9109-f8fc00013bb2'; //Город доставки string
  const ATTRIBUTE_CDEK_PVZ = 'ac26524e-6f98-11e8-9109-f8fc00013bb0'; //ПВЗ СДЭК customentity(customentitymetadata /entity/companysettings/metadata/customEntities/d083f36f-6be1-11e8-9ff4-31500008cb1f)
  const ATTRIBUTE_ADDRESS = 'ac264228-6f98-11e8-9109-f8fc00013bac'; //Адрес доставки string
  const ATTRIBUTE_CDEK_TARIFF = 'ac264ba7-6f98-11e8-9109-f8fc00013bae'; //Тарифы СДЭК customentity(/entity/companysettings/metadata/customEntities/d3b2f316-6ba7-11e8-9109-f8fc000dfa0f)
  const ATTRIBUTE_NB_PLACES = 'ac2655ae-6f98-11e8-9109-f8fc00013bb1'; //Кол-во мест string
  const ATTRIBUTE_COMMENT = 'ac265c42-6f98-11e8-9109-f8fc00013bb3'; //Комментарий string
  const ATTRIBUTE_PDF = '68be641e-7063-11e8-9107-5048000ea93e'; //PDF link
  const ATTRIBUTE_NO_PAYMENT = '97e6eea6-7065-11e8-9107-5048000ed3a1'; //Без оплаты boolean
  const ATTRIBUTE_WARRANTY = '3a64a28b-706b-11e8-9107-5048000f3e98'; //Страхование boolean*/

  private $sklad;
  private $eventDispatcher;
  private $organization_id;
  private $settingManager;


  public function __construct (MoyskladManager $sklad, EventDispatcherInterface $eventDispatcher, $organization_id,
                               SettingManagerInterface $settingManager)
  {
    $this->sklad = $sklad;
    $this->eventDispatcher = $eventDispatcher;
    $this->organization_id = $organization_id;
    $this->settingManager = $settingManager;
  }

  /**
   * @param Order $order
   * @throws \Accurateweb\MoyskladIntegrationBundle\Exception\MoyskladException
   * @throws RequestFailedException
   */
  public function sendOrder(Order $order)
  {
    
    $orid = $this->sklad->getRepository('MoySklad\\Entities\\Organization')->findAll();
    $organization = $orid[0];
    #$organization = $this->sklad->getRepository('MoySklad\\Entities\\Organization')->find($this->organization_id);

    if (!$organization)
    {
      throw new MoyskladException(sprintf('Organization %s was not found', $this->organization_id));
    }
    
    $contragentAddres = $order->getShippingAddress() ? $order->getShippingAddress() : '-';
    
    if($order->getShippingAddress() == null && $order->getShippingMethod()->getUid() === ShippingMethodStorePickup::UID)
    {
      if($order->getShippingMethod()->getAddress() !== null)
      {
        $contragentAddres = $order->getShippingMethod()->getAddress();
      }
    }
    
    $contragent = $this->getOrCreateContragentByEmail(
      $order->getCustomerEmail(),
      $order->getCustomerFullName(),
      $order->getCustomerPhone(),
      $contragentAddres
    );

    $customerOrder = new CustomerOrder($this->sklad->getSklad(), [
      'name' => $order->getDocumentNumber(),
      'sum' => $order->getTotal(),
      'externalCode' => $order->getDocumentNumber(),
      'moment' => $order->getCreatedAt()?$order->getCreatedAt()->format('Y-m-d H:i:s'):null,
      'created' => $order->getCreatedAt()?$order->getCreatedAt()->format('Y-m-d H:i:s'):null,
    ]);

    $customerOrderCreation = $customerOrder->buildCreation();
    $meta = $this->sklad->getRepository('MoySklad\\Entities\\Documents\\Orders\\CustomerOrder')->getClassMetadata();
    /**
     * Этот кусок на данный момент выпилен, ибо работает он с кастомными полями
     * @var EntityList $attributes
     */
   /* $attributes = $meta->attributes;
    foreach ($attributes as $attribute)
    {
      switch ($attribute->id)
      {
        case self::ATTRIBUTE_RECIEVER:
          $attribute->value = $order->getCustomerFullName();
          $customerOrderCreation->addAttribute($attribute);
          break;
        case self::ATTRIBUTE_RECIEVER_PHONE:
          $attribute->value = $order->getCustomerPhone();
          $customerOrderCreation->addAttribute($attribute);
          break;
        case self::ATTRIBUTE_ORDER_NUM:
          $attribute->value = $order->getDocumentNumber();
          $customerOrderCreation->addAttribute($attribute);
          break;
        case self::ATTRIBUTE_CDEK_PVZ:
          if ($order->getShippingMethodId() == ShippingMethodCdekTerminal::UID)
          {
            # При заказе через ПВЗ СДЕК в поле shipping_address лежит адрес из сдэка - подставляем его
            $shipping_info = $order->getShippingInfo();
            $addr = null;

            # Ищем по информации о ПВЗ
            if (isset($shipping_info['pvz']))
            {
              # @ var CdekRawPvzlist $pvz
              $pvz = $this->pvzlistRepository->find($shipping_info['pvz']);

              if ($pvz)
              {
                $addr = $pvz->getFullAddress();
              }
            }

            # Ищем по адресу заказа
            if (!isset($pvz))
            {
              $pvz = $this->pvzlistRepository->findOneBy(['address' => $order->getShippingAddress()]);

              if ($pvz)
              {
                $addr = $pvz->getFullAddress();
              }
            }

            if (!$addr)
            {
//              $this->logger->warning(sprintf('Pvz address not found for %s', $order->getDocumentNumber()));
              #Если ничего нет, вставляем просто адрес из заказа
              $addr = $order->getShippingAddress();
            }

            # Атрибут ПВЗ - справочник, поэтому делаем CustomEntity как атрибут
            $customAttribute = new CustomEntity($this->sklad->getSklad());
            $customAttribute->meta = $attribute->customEntityMeta;
            $customAttribute->name = $addr;

            $attribute->value = $customAttribute;
            $customerOrderCreation->addAttribute($attribute);
          }

          break;
        case self::ATTRIBUTE_CITY:
          $attribute->value = $order->getShippingCityName();
          $customerOrderCreation->addAttribute($attribute);
          break;
        case self::ATTRIBUTE_ADDRESS:
          if ($order->getShippingMethodId() != ShippingMethodCdekTerminal::UID)
          {
            # Для заказа через СДЕК Не нужно указывать адрес доставки
            $attribute->value = $order->getShippingAddress();
            $customerOrderCreation->addAttribute($attribute);
          }
          break;
        case self::ATTRIBUTE_COMMENT:
          $attribute->value = $order->getCustomerComment();
          $customerOrderCreation->addAttribute($attribute);
          break;
      }
    }*/

    $productList = $this->getProductList($order->getOrderItems());
    $customerOrderCreation
      ->addCounterparty($contragent)
      ->addPositionList($productList)
      ->addOrganization($organization);

    try
    {
      $customerOrderCreation->execute();
    }
    catch (ApiResponseException $e)
    {
      throw MoyskladExceptionFactory::throwException($e);
    }

    $this->eventDispatcher->dispatch('moysklad.order.create', new MoyskladOrderCreateEvent($customerOrder, $order));
  }
  
  /**
   * Отправляет в мой склад новый статус о извещении покупателя
   * @param Order $order
   * @throws \MoySklad\Exceptions\EntityCantBeMutatedException
   * @throws \Throwable
   * @return void
   */
  public function sendDeliveredMailStatus(Order $order)
  {
    # получить заказ
    $filter = new FilterQuery();
    $filter->eq('name', $order->getDocumentNumber());
    $sklad = $this->sklad->getSklad();
    
    $moySkladOrders = CustomerOrder::query($sklad)->filter($filter);
    
    $moySkladOrders = $moySkladOrders->toArray();
    if(count($moySkladOrders) == 0) return;
    
    $order = CustomerOrder::query($sklad)->byId($moySkladOrders[0]->id);
    
    $meta = $this->sklad->getRepository('MoySklad\\Entities\\Documents\\Orders\\CustomerOrder')->getClassMetadata();
    /** @var EntityList $attributes */
    $attributes = $meta->attributes;
    $attr = null;
    
    /** @var Attribute $attribute */
    foreach ($attributes as $attribute)
    {
      if($attribute->name == $this->settingManager->getSetting('moy_sklad.need_send_mail_after_getting.field_name')->getValue())
      {
        $customAttribute = new CustomEntity($this->sklad->getSklad());
        $customAttribute->meta = $attribute->customEntityMeta;
        $name = $this->settingManager->getSetting('moy_sklad.after_getting_mail_success_sent.value')->getValue();
        $customAttribute->name = $name;
        
        $attribute->value = $customAttribute;
        $attr = $attribute;
      }
    }
    
    if(null !== $attr) $order->buildUpdate()->addAttribute($attr)->execute();
  }
  /**
   * @param string $phone
   * @param string $fullName
   * @param string $email
   * @return Counterparty
   */
  protected function getOrCreateContragentByEmail($email, $fullName, $phone='', $actualAddress='')
  {
    $repository = $this->sklad->getRepository('MoySklad\\Entities\\Counterparty');
    $contragent = $repository
      ->findOneBy(['email' => $email]);

    if (!$contragent)
    {
      $contragent = $repository->createNewObject([
        'name' => $fullName,
        'phone' => $phone,
        'email' => $email,
        'actualAddress' => $actualAddress,
      ]);
    }

    return $contragent;
  }

  /**
   * Преобразуем товары из заказы в товары для отправки
   * @param $items OrderItem[]
   * @return array|EntityList
   * @throws MoyskladException
   */
  protected function getProductList($items)
  {
    $transformer = new OrderItemTransformer($this->sklad->getRepository('MoySklad\\Entities\\Products\\Product'));
    $list = new EntityList($this->sklad->getSklad());

    foreach ($items as $item)
    {
      try
      {
        $list->push($transformer->transform($item));
      }
      catch (\Exception $e)
      {
        throw new MoyskladException($e->getMessage());
      }
    }

    return $list;
  }
}