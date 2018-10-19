<?php

namespace AccurateCommerce\Shipping\Service;

use AccurateCommerce\Shipping\Method\ShippingMethod;

/**
 * Класс службы доставки
 *
 * @author Dancy
 */
abstract class ShippingService
{
  private $uid;
  
  private $shippingMethods;
  
  public function __construct($uid)
  {
    $this->uid = $uid;
    $this->shippingMethods = array();
    
    $this->configure();
  }
  
  protected function configure() {}

  public function getUid()
  {
    return $this->uid;
  }
  
  public function getShippingMethods()
  {
    return $this->shippingMethods;
  }
  
  protected function addShippingMethod(ShippingMethod $method)
  {
    $this->shippingMethods[] = $method;
  }
}
