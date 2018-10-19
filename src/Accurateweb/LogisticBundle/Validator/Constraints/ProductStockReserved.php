<?php

namespace Accurateweb\LogisticBundle\Validator\Constraints;


use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ProductStockReserved extends Constraint
{
  public function getTargets()
  {
    return self::CLASS_CONSTRAINT;
  }
}