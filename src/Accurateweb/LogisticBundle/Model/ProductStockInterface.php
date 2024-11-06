<?php

namespace Accurateweb\LogisticBundle\Model;


interface ProductStockInterface
{
  /**
   * @return StockableInterface
   */
  public function getProduct();

  /**
   * @return WarehouseInterface
   */
  public function getWarehouse();

  /**
   * Единиц товара на складе (полностью)
   * @return integer
   */
  public function getValue();

  /**
   * @param StockableInterface $product
   * @return $this
   */
  public function setProduct(StockableInterface $product);

  /**
   * @param $value int
   * @return $this
   */
  public function setValue($value);

  /**
   * @param WarehouseInterface $warehouse
   * @return $this
   */
  public function setWarehouse(WarehouseInterface $warehouse);

  /**
   * Количество зарезервированного товара (физически на складе, но недоступного для продажи)
   * @return integer
   */
  public function getReservedValue();

  /**
   * @param integer $value
   * @return $this
   */
  public function setReservedValue($value);

  /**
   * Количество товара, доступного для продажи
   * @return integer
   */
  public function getAvailableValue();
}