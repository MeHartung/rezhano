<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DelLinShippingService
 *
 * @author Dancy
 */
class KitShippingService extends ShippingService
{
  const UID = '4b9f1f8f-8de8-4305-ba66-992cb43e5858';
  
  public function __construct()
  {
    parent::__construct(self::UID);
  }
  
  protected function configure()
  {
    $this->addShippingMethod(new ShippingMethodKitCourier());
    $this->addShippingMethod(new ShippingMethodKitPickup());
  }
}
