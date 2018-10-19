<?php

namespace Accurateweb\LogisticBundle\Service\ProductAvailabilityManager;

use Accurateweb\LogisticBundle\Exception\StockableNotAvailableException;
use Accurateweb\LogisticBundle\Model\StockableInterface;

interface AvailabilityValidatorInterface
{
  /**
   * @param StockableInterface $stockable
   * @param integer $quantity
   * @throws StockableNotAvailableException
   */
  public function validate(StockableInterface $stockable, $quantity=1);
}