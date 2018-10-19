<?php

/*
 *  @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
namespace AccurateCommerce\Shipping\Method\Rupost;

use AccurateCommerce\Shipping\Api\Rupost\RussianPostCalcApi;
use AccurateCommerce\Shipping\Method\ShippingMethod;
use AccurateCommerce\Shipping\Method\ShippingMethod3rdPartyCompany;
use AccurateCommerce\Shipping\Shipment\Shipment;

/**
 * Способ доставки "Почта России"
 *
 * @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
class ShippingMethodRuPost extends ShippingMethod3rdPartyCompany
{
  
  const UID = 'ecc4f177-526e-471f-8f37-5608f1ca86bc';

  const RUSSIANPOSTCALC_API_KEY = 'c639929df519caf3f0571c2ba64096b9';
  const RUSSIANPOSTCALC_PASSWORD = 'SMUM29';

  
  private $lastEstimate = null;
  
  public function __construct()
  {
    parent::__construct(self::UID, ShippingMethod::CLSID_PICKUP, 'Почта России');
    
    $this->setDeferredEstimate(true);
  }

  public function estimate(Shipment $shipment)
  {
    $this->lastEstimate = parent::estimate($shipment);

    return $this->lastEstimate;
  }

  public function doEstimate(Shipment $shipment)
  {
    $estimate = $shipment->getShippingMethodSpecific($this->getUid(), 'estimate');
    if (null === $estimate)
    {
      $api = new RussianPostCalcApi(self::RUSSIANPOSTCALC_API_KEY, self::RUSSIANPOSTCALC_PASSWORD);

      $estimate = $api->estimate($shipment);

      $shipment->addShippingMethodSpecific($this->getUid(), 'estimate', $estimate ?: false);
    }

    return $estimate;
  }

  public function getPickupPoints()
  {
    $defaultPickupPoint = new \PickupPoint();
    $defaultPickupPoint->setName($this->getName());
    $defaultPickupPoint->setShippingEstimate($this->lastEstimate);
            
    return array($defaultPickupPoint);
  }

  public function isAvailable(Shipment $shipment)
  {
    if (parent::isAvailable($shipment))
    {
      //Максимальный вес посылки 20кг
      return $shipment->getWeight() <= 20;
    }
    
    return false;
  }
}
