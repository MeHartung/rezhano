<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 13.06.2017
 * Time: 14:42
 */

namespace AccurateCommerce\Shipping\Service\Excam;

use AccurateCommerce\Shipping\Method\Excam\ShippingMethodExcamCourier;
use AccurateCommerce\Shipping\Method\Excam\ShippingMethodExcamFree;
use AccurateCommerce\Shipping\Method\Excam\ShippingMethodExcamPickup;
use AccurateCommerce\Shipping\Method\ShippingMethodUserDefined;
use AccurateCommerce\Shipping\Service\ShippingService;

class ExcamShippingService extends ShippingService
{
  public function configure()
  {
    $this->addShippingMethod(new ShippingMethodExcamPickup());
    $this->addShippingMethod(new ShippingMethodExcamCourier());
//    $this->addShippingMethod(new ShippingMethodUserDefined());
//    $this->addShippingMethod(new ShippingMethodExcamFree());
  }
}