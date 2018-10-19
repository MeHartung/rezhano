<?php

namespace StoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class OgrnValidator extends ConstraintValidator
{
  public function validate ($value, Constraint $constraint)
  {
    if (!is_string($value))
    {
      $this->context->addViolation($constraint->message);
      return;
    }

    if (!preg_match('/^([0-9]{13}|[0-9]{15})$/', $value))
    {
      $this->context->addViolation($constraint->message);
      return ;
    }

    if (strlen($value) === 13)
    {
      $result = $this->validateOgrn($value);
    }
    else
    {
      $result = $this->validateOgrnIp($value);
    }

    if (!$result)
    {
      $this->context->addViolation($constraint->message);
    }
  }


  private function validateOgrn ($ogrn)
  {
    $ogrn = (string)$ogrn;

    if (preg_match('#([\d]{13})#', $ogrn, $m))
    {
      $code1 = substr($m[1], 0, 12);
      $code2 = floor($code1 / 11) * 11;
      $code = ($code1 - $code2) % 10;

      if ($code == $m[1][12])
      {
        return $m[1];
      }
    }

    return false;
  }

  private function validateOgrnip ($ogrn)
  {
    $ogrn = (string)$ogrn;

    if (preg_match('#([\d]{15})#', $ogrn, $m)) {
      $code1 = substr($m[1], 0, 14);
      $code2 = floor($code1 / 13) * 13;
      $code = ($code1 - $code2) % 10;

      if ($code == $m[1][14])
      {
        return $m[1];
      }
    }

    return false;
  }
}