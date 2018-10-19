<?php

namespace StoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class InnValidator extends ConstraintValidator
{
  public function validate ($value, Constraint $constraint)
  {
    if (!is_string($value))
    {
      $this->context->addViolation($constraint->message);
      return;
    }

    if (!preg_match('/^([0-9]{10}|[0-9]{12})$/', $value))
    {
      $this->context->addViolation($constraint->message);
      return;
    }

    switch (strlen($value))
    {
      case 10:
        $n10 = $this->checkDigit($value, [2, 4, 10, 3, 5, 9, 4, 6, 8]);

        if ($n10 !== (int)$value{9})
        {
          $this->context->addViolation($constraint->message);
          break;
        }

        break;
      case 12:
        $n11 = $this->checkDigit($value, [7, 2, 4, 10, 3, 5, 9, 4, 6, 8]);
        $n12 = $this->checkDigit($value, [3, 7, 2, 4, 10, 3, 5, 9, 4, 6, 8]);

        if (!(($n11 === (int)$value{10}) && ($n12 === (int)$value{11})))
        {
          $this->context->addViolation($constraint->message);
          break;
        }

        break;
    }
  }

  private function checkDigit ($inn, $coefficients)
  {
    $n = 0;

    foreach ($coefficients as $i => $k)
    {
      $n += $k * (int)$inn{$i};
    }

    return $n % 11 % 10;
  }
}