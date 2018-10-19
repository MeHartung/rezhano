<?php

namespace AccurateCommerce\Component\CdekShipping\Shipping\Method;

use AccurateCommerce\Component\CdekShipping\Api\CdekApiClient;
use AccurateCommerce\Shipping\Method\ShippingMethod;
use AccurateCommerce\Shipping\Pickup\PickupPointCollectionInterface;
use AccurateCommerce\Shipping\Shipment\Shipment;

/**
 * Способ доставки "Деловые Линии - до терминала"
 *
 * @author Dancy
 */
class ShippingMethodCdekTerminal extends ShippingMethodCdek implements PickupPointCollectionInterface
{
  const UID = '2fe0c750-9e0c-4c15-905a-efb4fe6a2b9b';
  
  public function __construct(CdekApiClient $api)
  {
    parent::__construct($api, self::UID, ShippingMethod::CLSID_PICKUP, 'До пункта самовывоза', 'Самовывоз из терминала «СДЭК»');
  }  
  
  public function doEstimate(Shipment $shipment)
  {
    $shippingInfo = $this->getShippingInfo($shipment, CdekApiClient::TARIFF_PARCEL_DOOR_STORAGE);
        
    return null !== $shippingInfo ? $shippingInfo->getPickupEstimate() : null;
  }
  
//  public function isAvailable(Shipment $shipment)
//  {
//    if (parent::isAvailable($shipment))
//    {
//      $shippingInfo = $this->getShippingInfo($shipment);
//      if (null !== $shippingInfo)
//      {
//        return $shippingInfo->isPickupAvailable();
//      }      
//    }
//    
//    return false;
//  }

  /**
   * Возвращает список пунктов выдачи для заданного адреса
   * 
   * @param Shipment $shipment Отправление, для которого запрашивается список пунктов самовывоза
   * @return IPickupPoint[]
   */  
  public function getPickupPoints(Shipment $shipment)
  {
    $shippingInfo = $this->getShippingInfo($shipment);
    if (null === $shippingInfo)
    {
      return array();
    }


    $pickupPoints = array();
    $estimate = $this->estimate($shipment);
    if ($shippingInfo)
    {
      $terminals = $shippingInfo->getPickupPoints();
      foreach ($terminals as $terminal)
      {
        $pickupPoint = new PickupPoint();
        $pickupPoint->setName($this->getName() . ' — '.$terminal['name']);
        
        $pickupPoint->setAddress(implode(', ', array_slice(explode(', ', $terminal['address']), -3)));
        $pickupPoint->setShippingEstimate($estimate);
        
        $geocode = json_decode(YandexGeocoderClient::geocode($terminal['address'], array('format' => 'json')), true);
        if (isset($geocode['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['Point']['pos']))
        {
          list($lat, $long) = explode(' ', $geocode['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['Point']['pos']);
          $pickupPoint->setGeoCoordinates($long.','.$lat);
        }
        
        $pickupPoints[] = $pickupPoint;
      }
    }
    
    return $pickupPoints;
  }

}
