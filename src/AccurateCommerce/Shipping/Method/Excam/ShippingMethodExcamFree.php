<?php

namespace AccurateCommerce\Shipping\Method\Store;

use AccurateCommerce\Shipping\Estimate\ShippingEstimate;
use AccurateCommerce\Shipping\Method\ShippingMethod;
use AccurateCommerce\Shipping\Shipment\Shipment;
use StoreBundle\Entity\Store\Order\OrderItem;

/**
 * Способ доставки "Бесплатная доставка"
 */
class ShippingMethodStoreFree extends ShippingMethod
{
  const UID = 'afb69151-962a-4a34-895a-3418b8a78682';

  protected $help = 'Способ и возможность бесплатной доставки уточнит оператор';

  public function __construct()
  {
    parent::__construct(self::UID, ShippingMethod::CLSID_SHIPPING, 'Бесплатная доставка');
  }

  /**
   * Бесплатная доставка должна быть бесплатной
   *
   * @param Shipment $shipment Отправление, для которого производится расчет доставки
   * @return ShippingEstimate
   */
  public function estimate (Shipment $shipment)
  {
    $estimate = new ShippingEstimate(0, null);
    return $estimate;
  }
  
  public function isAvailable(Shipment $shipment)
  {
    return $shipment->getOrder()->hasProductWithFreeDelivery();
  }
}
