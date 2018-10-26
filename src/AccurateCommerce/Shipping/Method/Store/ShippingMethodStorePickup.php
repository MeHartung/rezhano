<?php

namespace AccurateCommerce\Shipping\Method\Store;

use AccurateCommerce\Shipping\Estimate\ShippingEstimate;
use AccurateCommerce\Shipping\Pickup\PickupPointCollectionInterface;
use AccurateCommerce\Shipping\Method\ShippingMethod;
use AccurateCommerce\Shipping\Pickup\PickupPointInterface;
use AccurateCommerce\Shipping\Pickup\StaticPickupPoint;
use AccurateCommerce\Shipping\Shipment\Shipment;

/**
 * Способ получения "Пункты выдачи Спецрегион"
 *
 * @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
class ShippingMethodStorePickup extends ShippingMethodStore implements PickupPointCollectionInterface
{
  const UID = '8dc7ee8f-18f0-40af-964f-d10c3ab091a3';

  const ESTIMATE_UNKNOWN = 0;         //Покупатель сможет забрать заказ сегодня
  const ESTIMATE_TODAY_PARTIALLY = 1; //Покупатель сможет забрать часть заказа сегодня
  const ESTIMATE_TODAY = 2;           //Срок поставки уточнит оператор
  
  public function __construct()
  {
    parent::__construct(self::UID, ShippingMethod::CLSID_PICKUP, 'Самовывоз', 'Самовывоз из магазина «Режано»');
  }
  
  public function estimate(Shipment $shipment)
  {
    $estimate = $shipment->getShippingMethodSpecific($this->getUid(), 'estimate');
    if (null === $estimate)
    {
      $estimate = new ShippingEstimate(0, null);

      $shipment->addShippingMethodSpecific($this->getUid(), 'estimate', $estimate);
    }

    return $estimate;
  }
  
  /**
   * Возвращает список пунктов выдачи для заданного адреса
   * 
   * @param Shipment $shipment Отправление, для которого запрашивается список пунктов самовывоза
   * @return PickupPointInterface[]
   */
  public function getPickupPoints(Shipment $shipment)
  {
    return [
      new StaticPickupPoint($this->getName(), $this->estimate($shipment))
    ];
  }
}
