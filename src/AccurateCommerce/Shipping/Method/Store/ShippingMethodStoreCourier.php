<?php

/*
 *  @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */

namespace AccurateCommerce\Shipping\Method\Store;

use AccurateCommerce\Shipping\Estimate\ShippingEstimate;
use AccurateCommerce\Shipping\Method\ShippingMethod;
use AccurateCommerce\Shipping\Shipment\Shipment;

/**
 * Класс-заглушка для курьерской доставки "Store"
 *
 * @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
class ShippingMethodStoreCourier extends ShippingMethodStore
{
  const UID = 'eac20e0f-056a-4c10-9f43-7bee5c47167a';

  public function __construct()
  {
    parent::__construct(self::UID, ShippingMethod::CLSID_SHIPPING, 'Доставка');
  }

  /**
   * Возвращает оценку срока и стоимости доставки
   *
   * @param Shipment $shipment
   * @return ShippingEstimate
   */
  public function estimate(Shipment $shipment)
  {
    $estimate = $shipment->getShippingMethodSpecific($this->getUid(), 'estimate');

    if (null === $estimate)
    {
//      $cost = $shipment->getOrder()->getSubtotal() > 5000 ? 0 : 300;
      $cost = null;
      $estimate = new ShippingEstimate($cost, null);
      $shipment->addShippingMethodSpecific($this->getUid(), 'estimate', $estimate);
    }

    return $estimate;
  }

  public function isAvailable (Shipment $shipment)
  {
    return parent::isAvailable($shipment) && !$shipment->getOrder()->hasProductWithFreeDelivery();
  }

  public function getName($city = null)
  {
//    $city = is_null($city) ? $this->cityName : $city;

    if ($city)
    {
      /** При выборе города через Сдэк приходит "город, область, страна"  */
      $commaPosition = strpos($city, ',');
      $cityName = !$commaPosition ? $city : substr($city, 0, $commaPosition);

      $map = array(
        'Екатеринбург' => 'Екатеринбургу',
        'Москва' => 'Москве'
      );

      if (isset($map[$cityName]))
      {
        return sprintf('Доставка курьером по г. %s', $map[$cityName]);
      }

      return 'Доставка курьером';
    }

    return parent::getName();
  }
}
