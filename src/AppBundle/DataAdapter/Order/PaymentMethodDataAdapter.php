<?php

namespace AppBundle\DataAdapter\Order;


use AccurateCommerce\Component\Payment\Model\PaymentMethodManager;
use Accurateweb\ClientApplicationBundle\DataAdapter\ClientApplicationModelAdapterInterface;
use StoreBundle\Entity\Store\Order\Order;
use StoreBundle\Entity\Store\Payment\Method\PaymentMethod;

class PaymentMethodDataAdapter implements ClientApplicationModelAdapterInterface
{
  private $paymentMethodManager;

  public function __construct (PaymentMethodManager $paymentMethodManager)
  {
    $this->paymentMethodManager = $paymentMethodManager;
  }

  /**
   * @param PaymentMethod $subject
   * @param array $options
   * @return array
   */
  public function transform ($subject, $options = array())
  {
    $active = false;
    $fee = 0;

    if (isset($options['order']) && $options['order'] instanceof Order)
    {
      $active = $subject === $options['order']->getPaymentMethod();
      $fee = $this->paymentMethodManager->calculateFee($options['order'], $subject);
    }

    return [
      'id' => $subject->getId(),
      'name' => $subject->getName(),
      'enabled' => $subject->isEnabled(),
      'active' => $active,
      'help' => $subject->getDescription(),
      'fee' => $fee
    ];
  }

  public function getModelName ()
  {
    return 'PaymentMethod';
  }

  public function supports ($subject)
  {
    return $subject instanceof PaymentMethod;
  }
}