<?php

/*
 *  @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */

/**
 * Description of EmsShippingService
 *
 * @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
class EmsShippingService extends ShippingService
{
  const UID = '1191d22c-b31f-432d-92ce-46838236009b';
  
  public function __construct()
  {
    parent::__construct(self::UID);
  }
  
  protected function configure()
  {
    $this->addShippingMethod(new ShippingMethodEms());
  }
}
