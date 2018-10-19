<?php

namespace Accurateweb\LogisticBundle\Validator\Constraints;

use Accurateweb\LogisticBundle\Model\ProductStockInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ProductStockReservedValidator extends ConstraintValidator
{
  /**
   * @param ProductStockInterface $value
   * @param Constraint $constraint
   */
  public function validate ($value, Constraint $constraint)
  {
    if (!$value instanceof ProductStockInterface)
    {
      throw new UnexpectedTypeException($value, 'Accurateweb\LogisticBundle\Model\ProductStockInterface');
    }

    if ($value->getValue() < $value->getReservedValue())
    {
      $this->context->addViolation('Невозможно зарезервировать товара больше, чем есть на складе');
    }
  }
}