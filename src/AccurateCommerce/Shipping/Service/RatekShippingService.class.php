<?php

/*
 *  @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */

/**
 * Description of RatekShippingService
 *
 * @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
class RatekShippingService extends ShippingService
{
  const UID = 'aaf2cfb7-f0ef-46c1-9c78-6c4be05b32bd';
  
  public function __construct()
  {
    parent::__construct(self::UID);
  }
  
  protected function configure()
  {
    $this->addShippingMethod(new ShippingMethodRatekSib());
  }
}
