<?php

namespace Accurateweb\LogisticBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 *
 * @Annotation
 */
class OneWarehouseStock extends Constraint
{
  public $message = 'Товар должен находиться на одном складе';

  public function getTargets()
  {
    return self::PROPERTY_CONSTRAINT;
  }
}