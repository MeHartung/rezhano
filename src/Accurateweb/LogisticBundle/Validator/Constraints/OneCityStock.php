<?php

namespace Accurateweb\LogisticBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 *
 * @Annotation
 */
class OneCityStock extends Constraint
{
  public function getTargets()
  {
    return self::PROPERTY_CONSTRAINT;
  }
}