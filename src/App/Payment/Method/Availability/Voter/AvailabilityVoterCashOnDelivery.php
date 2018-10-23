<?php

namespace App\Payment\Method\Availability\Voter;

use AccurateCommerce\Component\CdekShipping\Shipping\Method\ShippingMethodCdek;
use AccurateCommerce\Component\Payment\Method\Availability\Voter\AvailabilityVoter;
use AccurateCommerce\Component\Payment\Model\PaymentMethod;
use AccurateCommerce\Shipping\Method\Rupost\ShippingMethodRuPost;
use AccurateCommerce\Shipping\Method\ShippingMethod3rdPartyCompany;
use StoreBundle\Entity\Store\Order\Order;

/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */
class AvailabilityVoterCashOnDelivery extends AvailabilityVoter
{
  public function vote(PaymentMethod $paymentMethod, Order $order)
  {
    $shippingMethod = $order->getShippingMethod();

    /*
     * Наложенный платеж недоступен, если доставка производится транспортной компанией. Исключение составляет Почта России.
     */
    return (($shippingMethod instanceof ShippingMethod3rdPartyCompany) &&
           !(($shippingMethod instanceof ShippingMethodRuPost) || ($shippingMethod instanceof ShippingMethodCdek))) ?
                AvailabilityVoter::UNAVAILABLE : AvailabilityVoter::AVAILABLE;
  }
}