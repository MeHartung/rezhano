<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 13.06.2017
 * Time: 14:42
 */

namespace AccurateCommerce\Shipping\Service\Store;

use AccurateCommerce\Shipping\Method\App\ShippingMethodStoreCourier;
use AccurateCommerce\Shipping\Method\App\ShippingMethodStoreFree;
use AccurateCommerce\Shipping\Method\App\ShippingMethodStorePickup;
use AccurateCommerce\Shipping\Method\ShippingMethodUserDefined;
use AccurateCommerce\Shipping\Service\ShippingService;

class StoreShippingService extends ShippingService
{
  public function configure()
  {
    $this->addShippingMethod(new ShippingMethodStorePickup());
    $this->addShippingMethod(new ShippingMethodStoreCourier());
//    $this->addShippingMethod(new ShippingMethodUserDefined());
//    $this->addShippingMethod(new ShippingMethodStoreFree());
  }
}