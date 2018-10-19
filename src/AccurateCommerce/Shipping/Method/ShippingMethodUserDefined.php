<?php

namespace AccurateCommerce\Shipping\Method;

use AccurateCommerce\Shipping\Shipment\Shipment;

/**
 * Способ доставки "Другая транспортная компания"
 *
 * @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
class ShippingMethodUserDefined extends ShippingMethod3rdPartyCompany
{
  const UID = '2cdc5b09-c88c-4698-93dd-50ffebff8364';

  /**
   * Конструктор.
   */
  public function __construct()
  {
    parent::__construct(self::UID, ShippingMethod::CLSID_PICKUP, 'Доставка транспортной компанией');
  }  
  
  /**
   * Выполняет оценку стоимости доставки. Всегда возвращает NULL, так как рассчитать стоимость доставки для 
   * неизвестной транспортной компании не представляется возможным
   * 
   * @param Shipment $shipment Отправление, для которого производится расчет доставки
   * @return null
   */
  public function doEstimate(Shipment $shipment)
  {
    return null;
  }
  
  public function getPickupPoints()
  {
    $defaultPickupPoint = new PickupPoint();
    $defaultPickupPoint->setName($this->getName());
    
    return array($defaultPickupPoint);
  }
  
  public function isAvailable(Shipment $shipment)
  {
    return parent::isAvailable($shipment);
  }
}
