<?php

namespace Accurateweb\LogisticBundle\Service\ProductStockManager;

use Accurateweb\LogisticBundle\Model\StockableInterface;
use Accurateweb\LogisticBundle\Model\WarehouseInterface;

interface ProductStockManagerInterface
{
  /**
   * Возвращает склад, с которого можно получить товар в указанном количестве
   * @param StockableInterface $product
   * @param int $quantity
   * @return WarehouseInterface|null
   */
  #public function getAvailableWarehouse(StockableInterface $product, $quantity=1);
  public function getAvailableWarehouse($product, $quantity=1);

  /**
   * Количество товара в указанном городе
   * @param StockableInterface $product
   * @param $city string
   * @return int
   */
  public function getStockByCity(StockableInterface $product, $city);
}