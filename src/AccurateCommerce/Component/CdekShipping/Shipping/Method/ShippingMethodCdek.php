<?php

/*
 *  @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */

namespace AccurateCommerce\Component\CdekShipping\Shipping\Method;

use AccurateCommerce\Component\CdekShipping\Api\CdekApiClient;
use AccurateCommerce\Component\CdekShipping\Api\CdekShippingInfo;
use AccurateCommerce\Shipping\Method\ShippingMethod3rdPartyCompany;
use AccurateCommerce\Shipping\Shipment\Shipment;

/**
 * Абстрактный класс способа доставки "СДЭК"
 *
 * @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
abstract class ShippingMethodCdek extends ShippingMethod3rdPartyCompany
{
  const UID = '7c8e2f3a-15ce-4cb7-8f98-29f701d8d747';

  protected $api;

  protected $tariffId;


  public function __construct(CdekApiClient $api, $tariffId, $uid, $clsid, $name, $internalName = null)
  {
    parent::__construct($uid, $clsid, $name, $internalName);

    $this->api = $api;
    $this->tariffId = $tariffId;

    $this->setDeferredEstimate(true);
  }

  /**
   * @return mixed
   */
  public function getTariffId()
  {
    return $this->tariffId;
  }

  /**
   * @param Shipment $shipment
   * @param $tariffId
   * @return \AccurateCommerce\Component\CdekShipping\Api\CdekShippingInfo
   */
  protected function getShippingInfo(Shipment $shipment, $tariffId=null)
  {
    if (null === $tariffId)
    {
      $tariffId = $this->getTariffId();
    }

    $shippingInfo = $shipment->getShippingMethodSpecific(self::UID, 'estimate');
    if (false === $shippingInfo)
    {
      return null;
    }

    if (!$shippingInfo instanceof CdekShippingInfo)
    { 
      try
      {
        $shippingInfo = $this->api->getShippingInfo($shipment->getSource(), $shipment->getDestination(), $shipment->getWeight(), $shipment->getVolume(), $tariffId);
        
        if ($shippingInfo instanceof CdekShippingInfo)
        {
          $shipment->addShippingMethodSpecific(self::UID, 'estimate', $shippingInfo);
        }
        else
        {
          $shipment->addShippingMethodSpecific(self::UID, 'estimate', false);
        }
      } 
      catch (Exception $e)
      {
        $shipment->addShippingMethodSpecific(self::UID, 'estimate', false);
        
        return null;
      }
    }

    return $shippingInfo;
  }
}
