<?php

namespace Accurateweb\LogisticBundle\Service\ProductStockManager;

use Accurateweb\LocationBundle\Service\Location;
use Accurateweb\LogisticBundle\Model\StockableInterface;

class ProductStockManager implements ProductStockManagerInterface
{
  private $location;

  /**
   * ProductStockManager constructor.
   * @param Location $location
   */
  public function __construct (Location $location)
  {
    $this->location = $location->getLocation();
  }

  /**
   * @inheritdoc
   */
  public function getAvailableWarehouse(StockableInterface $product, $quantity=1)
  {
    $stocks = $product->getStocks();
    $warehouse = null;

    if ($stocks)
    {
      foreach ($stocks as $stock)
      {
        if ($stock->getValue() >= $quantity)
        {
          $warehouse = $stock->getWarehouse();

          if ($warehouse->getCity()->getName() === $this->location->getCityName())
          {
            break;
          }
        }
      }
    }

    return $warehouse;
  }

  /**
   * @inheritdoc
   */
  public function getStockByCity(StockableInterface $product, $city)
  {
    $stocks = $product->getStocks();
    $total = 0;

    if ($stocks)
    {
      foreach ($stocks as $stock)
      {
        if ($stock->getWarehouse()->getCity()->getName() === $city)
        {
          $total += $stock->getValue();
        }
      }
    }

    return $total;
  }
}