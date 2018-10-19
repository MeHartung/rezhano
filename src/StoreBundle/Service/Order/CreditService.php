<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 16.11.17
 * Time: 10:41
 */

namespace StoreBundle\Service\Order;


use Doctrine\ORM\EntityManagerInterface;
use StoreBundle\Entity\Store\Catalog\Product\Product;
use StoreBundle\Entity\Store\Catalog\Product\ProductDelivery;
use StoreBundle\Entity\Store\Catalog\Taxonomy\Taxon;
use StoreBundle\Entity\Store\Order\Order;
use StoreBundle\Entity\Store\Order\OrderItem;
use StoreBundle\Entity\Store\Payment\Method\PaymentMethodType;
use StoreBundle\Validator\Constraints\ShippingMethod;
use Gedmo\Mapping\Annotation\Slug;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CreditService
{
  private $em;

  public function __construct(EntityManagerInterface $em)
  {
    $this->em = $em;
  }

  /**
   * Структура массива купивкредит
   */
  /**
   * $order = array(
   * Состав заказа
   * 'items' => array(
   * array(
   * 'title' => 'Товар-1',
   * 'category' => 'Категория товара 1',
   * 'qty' => 1,
   * 'price' => 3500
   * ), ...
   * ),
   * Информация о покупателе
   * 'details' => array(
   * 'firstname' => 'Иван',
   * 'lastname' => 'Иванов',
   * 'middlename' => 'Иванович',
   * 'email' => 'ivan@ivanov.com'
   * ),
   * 'partnerId' => 'a06m00000018y7rAAA', // ID Партнера в системе Банка (выдается Банком)
   * 'partnerOrderId' => 'test_order_'.uniqid(), // Уникальный номер заказа в системе Партнера
   * );
   */

  const TINKOFF_GUID = '2fe5f594-ddb8-4542-acda-e7b273df8e66';
  const ALFA_BANK_GUID = '536591a3-7641-4afe-86b8-8fc5572fce58';

  /**
   * Проверям, что юзер выбрал покупку в кредит
   * @param $paymentMethod int|string
   * @param $documentNumber string
   * @return boolean
   */
  public function isThisCredit($paymentMethod = null, $documentNumber = null )
  {
    if ($paymentMethod !== null)
    {
      if($paymentMethod == self::TINKOFF_GUID || $paymentMethod == self::ALFA_BANK_GUID)
      {
        return true;
      }
      else
      {
        return false;
      }
    }
    elseif($documentNumber !== null)
    {
      $paymentMethod = $this->getOrderPaymentMethodByDocumentNumber($documentNumber);

      if($paymentMethod == self::TINKOFF_GUID || $paymentMethod == self::ALFA_BANK_GUID)
      {
        return true;
      }
      else
      {
        return false;
      }
    }
    else
    {
      return false;
    }

  }

  /**
   * @param $documentNumber string
   * @return null|string
   */
  public function getOrderPaymentMethodByDocumentNumber($documentNumber)
  {
   $order = $this->em->getRepository(Order::class)->findOneBy(['documentNumber'=>$documentNumber]);
   return is_null($order) ? null : $order->getPaymentMethod()->getType();
  }

  public function getBankName($paymentMethod = null, $documentNumber = null)
  {

    if ($paymentMethod !== null)
    {
      if($paymentMethod == self::TINKOFF_GUID)
      {
        return 'tinkoff';
      }
      elseif($paymentMethod == self::ALFA_BANK_GUID)
      {
        return 'alfa-bank';
      }
      else
      {
        return null;
      }
    }
    elseif($documentNumber !== null)
    {
      $paymentMethod = $this->getOrderPaymentMethodByDocumentNumber($documentNumber);

      if($paymentMethod == self::TINKOFF_GUID)
      {
        return 'tinkoff';
      }
      elseif($paymentMethod == self::ALFA_BANK_GUID)
      {
        return 'alfa-bank';
      }
      else
      {
        return null;
      }
    }
    else
    {
      return null;
    }


  }


  /**
   * Проверяем заказ юзера и возращаем методы оплаты в кредит,
   * которые может выбрать юзер.
   * Ограничения:
   *  1) КупиВКредит
   *      - не м.б. использован при покупке на сумму менее 3 000
   *  2) Альфабанк
   *      - не м.б. использован если в заказе более 10 различных позиций
   *        (на кол-во одинаковых товаров ограничения нет)
   */
  public function whatCreditCanUse($order)
  {
    $disabledCreditPaymentMethods = [];

    if($order->getTotal() < 3001)
    {
      $disabledCreditPaymentMethods[] = ['id' => self::TINKOFF_GUID, 'help' => 'Сумма заказа должна быть больше 3000 рублей'];
    }

    if(count($order->getOrderItems()) > 10)
    {
      $disabledCreditPaymentMethods[] = ['id' => self::ALFA_BANK_GUID, 'help' => 'Количество позиций в заказе не мб больше 10'];
    }

    return $disabledCreditPaymentMethods;
  }


  public function alfaBankXmlSend(Order $order, $INN, $alfaUrl)
  {

    return $xml = $this->createXmlForAlfa($order, $INN);

 /*   $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $alfaUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
    $out = curl_exec($ch);
    curl_close($ch);

    return $out;*/
  }

  private function createXmlForAlfa(Order $order, $INN)
  {
    $xmlHeader = "
    <inParams>
      <companyInfo>
        <inn>" . $INN . "</inn>
      </companyInfo>
      <creditInfo>
        <reference>" . (string)$order->getDocumentNumber() . "</reference>
      </creditInfo>
      <clientInfo>
        <lastname>" . $order->getCustomerLastName() . "</lastname>
        <firstname>" . $order->getCustomerFirstName() . "</firstname>
        <email>" . $order->getCustomerEmail() . "</email>
        <mobphone>" . $this->getFormattedPhone($order->getCustomerPhone()) . "</mobphone>
      </clientInfo>
     <specificationList>";

        $xmlBody = '';

    /**
     * Не может быть более 10 товаров в одной позиции
     */
    if(count($order->getOrderItems()) > 10)
    {
      return new BadRequestHttpException('Не может быть более 10 товаров в 1 позиции по условияем банка');
    }

      foreach ($order->getOrderItems() as $orderItem)
      {
          /** @var $orderItem OrderItem */
        $product = $orderItem->getProduct();

        $taxon = $this->productTaxonForAlfaTaxon($orderItem->getProduct()->getTaxons(), $product);

        $xmlBody .= "
              <specificationListRow>
                <category>" . $taxon . "</category>
                <code>" . $product->getSku() . "</code>
                <description>" . $product->getName() . " </description>
                <amount>" . $orderItem->getQuantity() . "</amount>
                <price>" . $orderItem->getPrice() . "</price>
              </specificationListRow>";
      }

      $xmlFooter = "</specificationList>
          </inParams>"
        ;

    $xmlFull = $xmlHeader.$xmlBody.$xmlFooter;

    return $xmlFull;
  }

  /**
   * Поле описания товара - обязательно, макс. длина - 50 символов.
   * На данный момент просто обрезается описание товара.
   * @param $productDesc string
   * @return string
   */
  private function productDescriptionToXmlFormat($productDesc)
  {
    $productDescForXml = strip_tags($productDesc);
    $productDescForXml = str_replace('&nbsp;', '', $productDescForXml);
    $productDescForXml = trim($productDescForXml);
    $productDescForXml = mb_substr($productDescForXml, 0, 49);

    return $productDescForXml;
  }

  /**
   * У альфа банка свои категории товаров. Есть табл. соответсявия.
   * Не должно быть кириллицы.
   */
  private function productTaxonForAlfaTaxon($taxon, Product $product)
  {
    /**
     * Если у товара нет категории, то делаем его алиас категорией
     */
    $taxon = is_null($taxon) ? $product->getSlug() : $taxon->first();

    if($taxon instanceof Taxon)
    {
      $taxon = is_null($taxon->getAlfaBankTaxon()) ? $taxon->getSlug() : $taxon->getAlfaBankTaxon();
    }

    if(preg_match("/[a-z]/", $taxon))
    {
      $taxon = strtoupper($taxon);
    }

    return $taxon;
  }

  public function getFormattedPhone($phone)
  {
    $phone = str_replace(['-', ' ', '(', ')'], [''], $phone);
    $phone = str_replace('+7', '', $phone);

    return $phone;
  }
}