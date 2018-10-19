<?php

namespace Accurateweb\LogisticBundle\Service\ProductAvailabilityManager;

use Accurateweb\LogisticBundle\Exception\StockableNotAvailableException;
use Accurateweb\LogisticBundle\Model\StockableInterface;

class ProductAvailabilityManager
{
  /**
   * @var AvailabilityValidatorInterface[]
   */
  private $availabilityValidators;

  public function __construct ()
  {
    $this->availabilityValidators = [];
  }

  /**
   * @param StockableInterface $stockable
   * @param integer $quantity
   * @return boolean
   */
  public function isAvailable(StockableInterface $stockable, $quantity=1)
  {
    $isAvailable = true;

    foreach ($this->availabilityValidators as $availabilityValidator)
    {
      try
      {
        $availabilityValidator->validate($stockable, $quantity);
      }
      catch (StockableNotAvailableException $e)
      {
        $isAvailable = false;
        continue;
      }
    }

    return $isAvailable;
  }

  /**
   * @param StockableInterface $stockable
   * @param int $quantity
   * @throws StockableNotAvailableException
   */
  public function validate(StockableInterface $stockable, $quantity=1)
  {
    foreach ($this->availabilityValidators as $availabilityValidator)
    {
      $availabilityValidator->validate($stockable, $quantity);
    }
  }


  /**
   * @param AvailabilityValidatorInterface $availabilityResolver
   * @return $this
   */
  public function addAvailabilityValidator(AvailabilityValidatorInterface $availabilityResolver)
  {
    $this->availabilityValidators[] = $availabilityResolver;
    return $this;
  }
}