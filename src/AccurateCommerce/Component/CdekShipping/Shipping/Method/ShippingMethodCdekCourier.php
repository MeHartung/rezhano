<?php

namespace AccurateCommerce\Component\CdekShipping\Shipping\Method;

use AccurateCommerce\Component\CdekShipping\Api\CdekApiClient;
use AccurateCommerce\Shipping\Method\ShippingMethod;
use AccurateCommerce\Shipping\Shipment\Shipment;

/**
 * Способ доставки "Деловые Линии - до двери"
 *
 * @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
class ShippingMethodCdekCourier extends ShippingMethodCdek
{
  const UID = '0dcb6083-ab99-4a28-91c9-a77d05a1186d';
    
  public function __construct(CdekApiClient $api = null, $tariffId = CdekApiClient::TARIFF_PARCEL_DOOR_DOOR)
  {
    parent::__construct($api, $tariffId, self::UID, ShippingMethod::CLSID_SHIPPING, 'Курьером СДЭК до двери', null);
  }  
  
  public function doEstimate(Shipment $shipment)
  {
    $shippingInfo = $this->getShippingInfo($shipment);
    
    return null !== $shippingInfo ? $shippingInfo->getShippingEstimate() : null;
  }  
}
