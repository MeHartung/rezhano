<?php

namespace AccurateCommerce\Component\Payment\Method\Availability\Voter;

use AccurateCommerce\Component\Payment\Model\PaymentMethod;
use StoreBundle\Entity\Store\Order\Order;

/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */
abstract class AvailabilityVoter
{
  const AVAILABLE = 1;
  const UNAVAILABLE = 0;
  const ABSTAIN = -1;

  abstract public function vote(PaymentMethod $paymentMethod, Order $order);
}