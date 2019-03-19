<?php

namespace Accurateweb\MoyskladIntegrationBundle\Service;

use AccurateCommerce\Component\CdekShipping\Shipping\Method\ShippingMethodCdekTerminal;
use AccurateCommerce\Shipping\Method\Store\ShippingMethodStoreCourier;
use AccurateCommerce\Shipping\Method\Store\ShippingMethodStorePickup;
use Accurateweb\MoyskladIntegrationBundle\Event\MoyskladOrderCreateEvent;
use Accurateweb\MoyskladIntegrationBundle\Exception\MoyskladException;
use Accurateweb\MoyskladIntegrationBundle\Exception\MoyskladExceptionFactory;
use Accurateweb\MoyskladIntegrationBundle\Model\Logistic\MoySkladWarehouse;
use Accurateweb\MoyskladIntegrationBundle\Model\MoyskladManager;
use Accurateweb\MoyskladIntegrationBundle\Model\OrderItemToBundleTransformer;
use Accurateweb\MoyskladIntegrationBundle\Model\OrderItemTransformer;
use Accurateweb\SettingBundle\Model\Manager\SettingManagerInterface;
use MoySklad\Entities\Store;
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
  # обычный товар в МС
  const PRODUCT_TYPE_SINGLE = 'single';
  # составной товар из МС
  const PRODUCT_TYPE_BUNDLE = 'bundle';
  
  const ATTRIBUTE_STORE_ORDER_NUM = 'cea238d58-2706-11e9-9109-f8fc000e93ad'; //Номер заказа на стороне ИМ
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
  const ATTRIBUTE_DELIVERY_ADDRESS = '1578a4e6-368a-11e9-9ff4-34e800100a66'; //Адрес доставки
//  const ATTRIBUTE_DELIVERY_ADDRESS = '841cfebe-40b2-11e9-9109-f8fc0002f82b'; //Адрес доставки
  const ATTRIBUTE_PHONE_NUMBER = 'bdb131e9-4583-11e9-9107-50480012cbf0'; //Контактный номер телефона

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
    $warehouse = $this->getWarehouse($order->getShippingCityName());
    
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
      $contragentAddres,
      $order->getCustomerType()
    );

    $customerOrder = new CustomerOrder($this->sklad->getSklad(), [
      # 'name' => $order->getDocumentNumber(), # возлагаем генерацию id на МС
      'sum' => $order->getTotal(),
      'externalCode' => $order->getDocumentNumber(),
      'moment' => $order->getCheckoutAt()?$order->getCheckoutAt()->format('Y-m-d H:i:s'):null,
      'created' => $order->getCheckoutAt()?$order->getCheckoutAt()->format('Y-m-d H:i:s'):null,
      'applicable' => false, # заказ не проведён (черновик)
      # валидация на стороне МС не примет null
      'description' => $order->getCustomerComment() ? $order->getCustomerComment() : ''
    ]);

    $customerOrderCreation = $customerOrder->buildCreation();
    if($warehouse !== null)
    {
      $customerOrderCreation->addStore($warehouse);
    }
    $meta = $this->sklad->getRepository('MoySklad\\Entities\\Documents\\Orders\\CustomerOrder')->getClassMetadata();
    /**
     * Этот кусок на данный момент выпилен, ибо работает он с кастомными полями
     * @var EntityList $attributes
     */
    $attributes = $meta->attributes;

    foreach ($attributes as $attribute)
    {
      switch ($attribute->id)
      {
        case self::ATTRIBUTE_STORE_ORDER_NUM:
          $attribute->value = $order->getDocumentNumber();
          $customerOrderCreation->addAttribute($attribute);
          break;
        case self::ATTRIBUTE_DELIVERY_ADDRESS:
          $attribute->value = $contragentAddres;
          $customerOrderCreation->addAttribute($attribute);
          break;
        case self::ATTRIBUTE_PHONE_NUMBER:
          $attribute->value = $order->getCustomerPhone();
          $customerOrderCreation->addAttribute($attribute);
          break;
      }
    }

    $productList = $this->getProductList($order->getOrderItems());
    $bundleList = $this->getBundleList($order->getOrderItems());
    
    $customerOrderCreation
      ->addCounterparty($contragent)
      ->addPositionList($productList)
      ->addPositionList($bundleList)
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
   * @param string $customerType - тип покупателя, юр или физ. лицо
   * @return Counterparty
   */
  protected function getOrCreateContragentByEmail($email, $fullName, $phone='', $actualAddress='',
                                                  $customerType = Order::CUSTOMER_TYPE_INDIVIDUAL)
  {
    $repository = $this->sklad->getRepository('MoySklad\\Entities\\Counterparty');
    $contragent = $repository
      ->findOneBy(['email' => $email]);

    if (!$contragent)
    {
      $settingName = $customerType == Order::CUSTOMER_TYPE_INDIVIDUAL ? 'individual_customer_tag' : 'legal_customer_tag';
      
      $contragent = $repository->createNewObject([
        'name' => $fullName,
        'phone' => $phone,
        'email' => $email,
        'actualAddress' => $actualAddress,
        'tags' => [$this->settingManager->getSetting($settingName)->getValue()],
        'companyType' => $customerType
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
      if($item->getProduct()->isBundle()) continue;
      
      try
      {
        $list->push($transformer->transform($item));
      } catch (\Exception $e)
      {
        throw new MoyskladException($e->getMessage());
      }
    }
  
    return $list;
  }
  
  /**
   * Преобразуем товары из заказы в товары для отправки
   * @param $items OrderItem[]
   * @return array|EntityList
   * @throws MoyskladException
   */
  protected function getBundleList($items)
  {
    $transformer = new OrderItemToBundleTransformer($this->sklad->getRepository('MoySklad\\Entities\\Products\\Bundle'));
    $list = new EntityList($this->sklad->getSklad());

    foreach ($items as $item)
    {
      if(!$item->getProduct()->isBundle()) continue;
  
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
  
  /**
   * Возращает склад для города, если он есть
   * @param $cityName string
   * @return Store|null
   */
  private function getWarehouse($cityName)
  {
    /** @var MoySkladWarehouse|null $warehouse */
    $warehouse = null;
    $repo = $this->sklad->getRepository('MoySklad\\Entities\\Store');
    $warehouseInternal = null;
    
    if($cityName === 'Реж')
    {
      $warehouseInternal = $this->settingManager->getSetting('warehouse_rezh')->getValue();
    }elseif($cityName === 'Екатеринбург')
    {
      $warehouseInternal = $this->settingManager->getSetting('warehouse_ekb')->getValue();
    }else
    {
      $warehouseInternal = $this->settingManager->getSetting('warehouse_other_city')->getValue();
    }
    /** @var  $warehouseInternal MoySkladWarehouse */
    if($warehouseInternal)
    {
      $warehouse = $repo->find($warehouseInternal->getExternalId());
    }
    
    return $warehouse;
  }
}