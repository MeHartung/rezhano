<?php

namespace Accurateweb\LogisticBundle\Model;

interface StockableInterface
{
  /**
   * Aggregated column
   * @param integer|null $stock
   * @return $this
   */
  public function setTotalStock($stock=null);

  /**
   * @return integer|null
   */
  public function getTotalStock();

  /**
   * Aggregated column
   * @param integer|null $stock
   * @return $this
   */
  public function setReservedStock($stock=null);

  /**
   * @return integer
   */
  public function getReservedStock();

  /**
   * @return integer
   */
  public function getAvailableStock();

  /**
   * @param boolean $isInStock
   * @return $this
   */
  public function setInStock($isInStock);

  /**
   * @return boolean
   */
  public function getInStock();

  /**
   * @return ProductStockInterface[]
   */
  public function getStocks();

  /**
   * @param ProductStockInterface[] $stocks
   * @return $this
   */
  public function setStocks($stocks);

  /**
   * @param ProductStockInterface $stock
   * @return $this
   */
  public function addStock(ProductStockInterface $stock);

  /**
   * @param ProductStockInterface $stock
   * @return $this
   */
  public function removeStock(ProductStockInterface $stock);
}