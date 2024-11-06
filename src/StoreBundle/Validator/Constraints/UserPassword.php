<?php

namespace StoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UserPassword extends Constraint
{
  public function getTargets()
  {
    return self::CLASS_CONSTRAINT;
  }
}