<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Validator\Constraints;

use AccurateCommerce\Component\Payment\Model\PaymentMethodManager;
use StoreBundle\Entity\Store\Order\Order;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PaymentMethodValidator extends ConstraintValidator
{
  private $paymentManager;

  function __construct(PaymentMethodManager $paymentManager)
  {
    $this->paymentManager = $paymentManager;
  }

  /**
   * Checks if the passed value is valid.
   *
   * @param Order $order The value that should be validated
   * @param Constraint $constraint The constraint for the validation
   */
  public function validate($order, Constraint $constraint)
  {
    $paymentMethod = $order->getPaymentMethod();

    if (!$paymentMethod)
    {
      return;
    }

    $availablePaymentMethods = $this->paymentManager->getAvailablePaymentMethods($order);
    $isValid = false;

    foreach ($availablePaymentMethods as $availablePaymentMethod)
    {
      if ($availablePaymentMethod->getId() == $paymentMethod->getId())
      {
        $isValid = true;
        break;
      }
    }

    if (!$isValid) {
      $this->context->buildViolation($constraint->message)
        ->atPath('payment_method')
        ->setParameter('payment_method', (string)$order->getPaymentMethod())
        ->addViolation();
    }
  }

}