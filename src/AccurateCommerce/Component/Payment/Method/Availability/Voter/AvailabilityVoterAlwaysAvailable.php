<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace AccurateCommerce\Component\Payment\Method\Availability\Voter;

use AccurateCommerce\Component\Payment\Model\PaymentMethod;
use StoreBundle\Entity\Store\Order\Order;

class AvailabilityVoterAlwaysAvailable extends AvailabilityVoter
{
  public function vote(PaymentMethod $paymentMethod, Order $order)
  {
    return AvailabilityVoter::AVAILABLE;
  }
}