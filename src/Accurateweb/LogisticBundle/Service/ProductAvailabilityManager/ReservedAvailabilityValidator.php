<?php

namespace Accurateweb\LogisticBundle\Service\ProductAvailabilityManager;


use Accurateweb\LogisticBundle\Exception\StockableNotAvailableException;
use Accurateweb\LogisticBundle\Model\StockableInterface;

class ReservedAvailabilityValidator implements AvailabilityValidatorInterface
{
  /**
   * @inheritdoc
   */
  public function validate (StockableInterface $stockable, $quantity = 1)
  {
    $availableStock = $stockable->getAvailableStock();
    $totalStock = $stockable->getTotalStock();

    if ($quantity <= $totalStock && $quantity > $availableStock)
    {
      throw new StockableNotAvailableException(sprintf('Товар не доступен в количестве %s, т.к. был зарезервирован', $quantity));
    }
  }
}