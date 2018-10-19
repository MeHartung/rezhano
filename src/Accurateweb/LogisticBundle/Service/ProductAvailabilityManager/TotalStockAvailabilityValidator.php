<?php

namespace Accurateweb\LogisticBundle\Service\ProductAvailabilityManager;

use Accurateweb\LogisticBundle\Exception\StockableNotAvailableException;
use Accurateweb\LogisticBundle\Model\StockableInterface;

class TotalStockAvailabilityValidator implements AvailabilityValidatorInterface
{
  /**
   * @inheritdoc
   */
  public function validate (StockableInterface $stockable, $quantity = 1)
  {
    if ($quantity > $stockable->getTotalStock())
    {
      throw new StockableNotAvailableException(sprintf('Товар в количестве %s отсутствет на складе', $quantity));
    }
  }
}