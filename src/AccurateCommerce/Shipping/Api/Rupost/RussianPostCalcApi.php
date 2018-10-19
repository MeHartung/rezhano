<?php

/*
 *  @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */

namespace AccurateCommerce\Shipping\Api\Rupost;

use AccurateCommerce\Shipping\Estimate\ShippingEstimate;
use AccurateCommerce\Shipping\Shipment\Shipment;

/**
 * http://russianpostcalc.ru/api-devel.php
 *
 * @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
class RussianPostCalcApi
{
  const URL = 'http://russianpostcalc.ru/api_v1.php';
  
  private $apiKey,
          $password;
  
  private function post($data)
  {
    if (!is_array($data))
    {
      throw new InvalidArgumentException();
    }
    
    if ($this->password)
    {        
      $md5Data = $data;
      $md5Data[] = $this->password;

      $hash = md5(implode("|", $md5Data));

      $data["hash"] = $hash;
    }
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, self::URL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);

    curl_close($ch);

    if (false === $response)
    {
      throw new RussianPostCalcApiException(curl_error($ch));
    }

    return json_decode($response, true);
  }  
  
  public function __construct($apiKey, $password=null)
  {
    $this->apiKey = $apiKey;
    $this->password = $password;        
  }
  
  public function estimate(Shipment $shipment)
  {
    $cost = null;
    $duration = null;
    
    try
    {
      $result = $this->post(array(
        'apikey' => $this->apiKey, 
        'method' => 'calc', 
        'from_index' => $shipment->getSource()->getPostCode(),
        'to_index' => $shipment->getDestination()->getPostCode(),
        'weight' => $shipment->getWeight(),
        'ob_cennost_rub' => 0//$shipment->getDeclaredValue()
      ));
      
      if (isset($result['calc']))
      {        
        foreach ($result['calc'] as $calculation)
        {
          if ($calculation['type'] == 'rp_main')
          {
            $cost = $calculation['cost'];
            $duration = $calculation['days'];
            
            break;
          }
          else
          {
            if (null === $cost || $calculation['cost'] < $cost)
            {
              $cost = $calculation['cost'];
            }
            if (null === $duration || $calculation['days'] < $duration)
            {
              $duration = $calculation['days'];
            }
          }
        }
      }
    }
    catch (RussianPostCalcApiException $e)
    {
      
    }

    return new ShippingEstimate($cost, $duration);
  }
}
