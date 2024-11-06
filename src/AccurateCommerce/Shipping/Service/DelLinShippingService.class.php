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
class DelLinShippingService extends ShippingService
{
  const UID = '2c739015-9d4e-4289-b036-902df3fd48eb';
  
  public function __construct()
  {
    parent::__construct(self::UID);
  }
  
  protected function configure()
  {
    $this->addShippingMethod(new ShippingMethodDelLinTerminal());
    $this->addShippingMethod(new ShippingMethodDelLinCourier());
  }
}
