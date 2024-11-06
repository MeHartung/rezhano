<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace AccurateCommerce\Component\Payment\Method\Availability;

use AccurateCommerce\Component\Payment\Method\Availability\Voter\AvailabilityVoter;
use AccurateCommerce\Component\Payment\Model\PaymentMethod;
use StoreBundle\Entity\Store\Order\Order;

class AvailabilityDecisionManager
{
  /**
   * @var AvailabilityVoter[]
   */
  private $voters;

  private $id;

  /**
   * @var string
   */
  private $name;

  /**
   * @return mixed
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * AvailabilityDecisionManager constructor.
   * @param $id
   * @param $name
   * @param array $voters
   */
  public function __construct($id, $name, $voters=array())
  {
    $this->voters = $voters;

    $this->id = $id;
    $this->name = $name;
  }

  public function addVoter(AvailabilityVoter $voter)
  {
    $this->voters[] = $voter;
  }

  public function decide(Order $order, PaymentMethod $paymentMethod)
  {
    foreach ($this->voters as $voter)
    {
//      if (!$voter->supports($paymentMethod))
//      {
//        continue;
//      }

      $vote = $voter->vote($paymentMethod, $order);
      switch ($vote)
      {
        case AvailabilityVoter::UNAVAILABLE:
          return false;

        default:
          break;
      }
    }

    return true;
  }

  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }


}