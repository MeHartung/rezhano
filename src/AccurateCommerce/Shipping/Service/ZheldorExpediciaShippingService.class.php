<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ZheldorExpediciaShippingService
 *
 * @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
class ZheldorExpediciaShippingService extends ShippingService
{
    const UID = 'a5d69a55-a85b-4e9f-924d-eac7f13c3d82';
  
  public function __construct()
  {
    parent::__construct(self::UID);
  }
  
  public function configure()
  {
    $this->addShippingMethod(new ShippingMethodZheldorExpedicia());
  }
}
