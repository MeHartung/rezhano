<?php

/*
 *  @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */

namespace AccurateCommerce\Shipping\Api\Rupost;

use AccurateCommerce\Shipping\Estimate\ShippingEstimate;

/**
 * 
 * @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
class PrintPostComApi
{
  const COST_API_URL = "http://api.print-post.com/api/";
  const POST_INDEX_API_URL = "http://fcl.russianpost.ru/pofs/AddressComparison.asmx/FindOPSByContextStrServicesAndPeriods";

  private function findPostIndex($address)
  {
    $parameters = array(
      "ContextStr" => urlencode($address),
      "Services" => "null",
      "Workdays" => "null",
      "BegWorktime" => "null",
      "EndWorktime" => "null"
    );

    $response = json_decode($this->sendRequest(http_build_query($parameters), self::POST_INDEX_API_URL));

    return isset($response[1]->Index) ? $response[1]->Index : null;
  }  
  
  public function estimate(Shipment $shipment)
  {
    $destinationPostalCode = $shipment->getDestination()->getPostCode();
    if (!$destinationPostalCode)
    {
      $destinationPostalCode = $this->findPostIndex(sprintf("%s %s", 
              $shipment->getDestination()->getCityName(), $shipment->getDestination()->getAddress()));
    }
    
    $parameters = array(
        'callback' => 'res',
        'weight' => $shipment->getWeight(),
        'sum' => $shipment->getDeclaredValue(),
        'from_index' => $shipment->getSource()->getPostCode(),
        'to_index' => $destinationPostalCode
    );

    $apiUrl = sprintf("%ssendprice/v1/", self::COST_API_URL);

    if (preg_match("/res\((.*[\s\S]*?)\)/si", $this->sendRequest(http_build_query($parameters), $apiUrl), $matches) > 0)
    {
      $res = json_decode($matches[1]);
    }
    else
    {
      $res = null;
    }

    if (!isset($res->posilka) OR is_null($res->posilka))
    {
      return null;
    }

    $deliveryCost = ceil(str_replace(",", ".", $res->posilka));
    if (!$deliveryCost)
    {
      $deliveryCost = null;
    }    
    
    return new ShippingEstimate($deliveryCost, null);
  }
}
