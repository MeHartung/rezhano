<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 25.06.2017
 * Time: 13:57
 */

namespace AccurateCommerce\Component\Payment\Method\Fee;


class FeeCalculatorRepository
{
  private $calculators;

  public function __construct()
  {
    $this->calculators = array();
  }

  public function addCalculator(FeeCalculatorInterface $feeCalculator)
  {
    $this->calculators[$feeCalculator->getId()] = $feeCalculator;
  }

  /**
   * @param $id
   * @return FeeCalculatorInterface|null
   */
  public function find($id)
  {
    return isset($this->calculators[$id]) ? $this->calculators[$id] : null;
  }

  public function findAll()
  {
    return $this->calculators;
  }

}