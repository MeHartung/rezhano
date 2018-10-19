<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 08.11.2017
 * Time: 20:32
 */

namespace AccurateCommerce\Shipping\Method\Excam;

use AccurateCommerce\Shipping\Method\ShippingMethod;
use AccurateCommerce\Shipping\Shipment\Shipment;

abstract class ShippingMethodExcam extends ShippingMethod
{
//  protected $cityName = 'Екатеринбург';

  /**
   * Курьерская доставка реализована только в г. Екатеринбург
   *
   * @param Shipment $shipment
   * @return boolean
   */
  public function isAvailable(Shipment $shipment)
  {
    return true;
  }
}