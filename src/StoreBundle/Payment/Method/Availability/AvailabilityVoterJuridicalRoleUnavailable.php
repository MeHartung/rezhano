<?php

namespace StoreBundle\Payment\Method\Availability;


use AccurateCommerce\Component\Payment\Method\Availability\Voter\AvailabilityVoter;
use AccurateCommerce\Component\Payment\Model\PaymentMethod;
use StoreBundle\Entity\Store\Order\Order;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

/*
 * Недоступность юридическому лицу
 */
class AvailabilityVoterJuridicalRoleUnavailable extends AvailabilityVoter
{
  private $authorizationChecker;

  public function __construct (AuthorizationChecker $authorizationChecker)
  {
    $this->authorizationChecker = $authorizationChecker;
  }

  /**
   * @inheritdoc
   */
  public function vote (PaymentMethod $paymentMethod, Order $order)
  {
    if ($this->authorizationChecker->isGranted('ROLE_JURIDICAL'))
    {
      return self::UNAVAILABLE;
    }

    return self::AVAILABLE;
  }
}