<?php

namespace Accurateweb\LogisticBundle\Validator\Constraints;


use Accurateweb\LogisticBundle\Model\ProductStockInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class OneWarehouseStockValidator extends ConstraintValidator
{
  /**
   * @param ProductStockInterface[]|ArrayCollection $stocks
   * @param Constraint $constraint
   */
  public function validate ($stocks, Constraint $constraint)
  {
    $total = 0;
    $warehouses = [];

    /** @var ProductStockInterface $stock */
    foreach ($stocks as $stock)
    {
      $warehouse = $stock->getWarehouse()->getId();

      if (!isset($warehouses[$warehouse]))
      {
        $warehouses[$warehouse] = 0;
      }

      $warehouses[$warehouse] += $stock->getValue();
      $total += $stock->getValue();
    }

    if (count($warehouses) && $total && $total !== max($warehouses))
    {
      $this->context->addViolation($constraint->message);
    }
  }
}