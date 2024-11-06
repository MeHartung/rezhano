<?php

/*
 *  @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
namespace AccurateCommerce\Shipping\Service;

use AccurateCommerce\Shipping\Method\Rupost\ShippingMethodRuPost;

/**
 * Description of RuPostShippingService
 *
 * @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
class RuPostShippingService extends ShippingService
{
  protected function configure()
  {
    $this->addShippingMethod(new ShippingMethodRuPost());
  }
}
