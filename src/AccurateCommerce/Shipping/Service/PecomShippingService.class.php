<?php

/*
 *  @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */

/**
 * Служба доставки ПЭК
 *
 * @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
class PecomShippingService extends ShippingService
{
  const UID = '29208f08-97ba-4e7f-9091-c715f4434b73';
  
  public function __construct()
  {
    parent::__construct(self::UID);
  }
  
  protected function configure()
  {
    $this->addShippingMethod(new ShippingMethodPecomPickup());
    $this->addShippingMethod(new ShippingMethodPecomCourier());
  }
}
