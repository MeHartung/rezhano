<?php

namespace Accurateweb\LogisticBundle\Validator\Constraints;

use Accurateweb\LogisticBundle\Model\ProductStockInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class OneCityStockValidator extends ConstraintValidator
{
  /**
   * @param ProductStockInterface[]|ArrayCollection $stocks
   * @param Constraint $constraint
   */
  public function validate ($stocks, Constraint $constraint)
  {
    $cities = [];
    $total = 0;

    /** @var ProductStockInterface $stock */
    foreach ($stocks as $stock)
    {
      $cityName = $stock->getWarehouse()->getCity()->getName();

      if (!isset($cities[$cityName]))
      {
        $cities[$cityName] = 0;
      }

      $cities[$cityName] += $stock->getValue();
      $total += $stock->getValue();
    }

    if ($total !== max($cities))
    {
      $this->context->addViolation('Товар должен находиться на складах одного города');
    }
  }
}