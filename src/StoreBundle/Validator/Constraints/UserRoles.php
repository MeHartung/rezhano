<?php

namespace StoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 *
 * @Annotation
 */
class UserRoles extends Constraint
{
  public function getTargets()
  {
    return self::CLASS_CONSTRAINT;
  }
}