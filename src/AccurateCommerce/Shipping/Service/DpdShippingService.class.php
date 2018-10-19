<?php

/*
 * Автор Денис Н. Рагозин <dragozin at accurateweb.ru>
 */

/**
 * Служба доставки DPD
 * 
 * http://www.dpd.ru/
 *
 * @author Денис Н. Рагозин <dragozin at accurateweb.ru>
 */
class DpdShippingService extends ShippingService
{
  const UID = '2eae83cd-d486-466f-90b2-8c5b9b8e0323';
  
  public function __construct()
  {
    parent::__construct(self::UID);
  }
  
  protected function configure()
  {
    $this->addShippingMethod(new ShippingMethodDpdPickup());
    $this->addShippingMethod(new ShippingMethodDpdCourier());
  }
}
