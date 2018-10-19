<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace AccurateCommerce\Shipping\Method;

use AccurateCommerce\Shipping\Estimate\ShippingEstimate;
use AccurateCommerce\Shipping\Shipment\Shipment;

interface ShippingMethodInterface
{
  /**
   * @return ShippingEstimate
   */
  public function estimate(Shipment $shipment);

  public function isAvailable(Shipment $shipment);
    
  public function getHelp();
}