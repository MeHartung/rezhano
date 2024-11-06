<?php

namespace StoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class KppValidator extends ConstraintValidator
{
  public function validate ($value, Constraint $constraint)
  {
    if (!preg_match('/^[0-9]{4}[0-9A-Z]{2}[0-9]{3}$/', $value))
    {
      $this->context->addViolation($constraint->message);
    }
  }
}