<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Validator\Constraints;


use Symfony\Component\Validator\Constraint;

/**
 * Description of ShippingMethod
 * @package StoreBundle\Validator\Constraints
 *
 * @Annotation
 */
class ShippingMethod extends Constraint
{
  public $message = 'Способ доставки «shipping_method_id» недоступен для данного заказа';

  public function getTargets()
  {
    return self::CLASS_CONSTRAINT;
  }
}