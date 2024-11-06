<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Validator\Constraints;


use Symfony\Component\Validator\Constraint;

/**
 * Description of PaymentMethodConstraint
 * @package StoreBundle\Validator\Constraints
 *
 * @Annotation
 */
class PaymentMethod extends Constraint
{
  public $message = 'Способ оплаты «{{ payment_method_id }}» недоступен для данного заказа';

  public function getTargets()
  {
    return self::CLASS_CONSTRAINT;
  }
}