<?php

namespace StoreBundle\DataAdapter\Cart;

use Accurateweb\ClientApplicationBundle\DataAdapter\ClientApplicationModelAdapterInterface;
use Accurateweb\LogisticBundle\Service\ProductStockManager\ProductStockManagerInterface;
use Accurateweb\MediaBundle\Model\Image\Image;
use StoreBundle\DataAdapter\Logistic\WarehouseDataAdapter;
use StoreBundle\DataAdapter\Product\ProductDataAdapter;
use StoreBundle\Entity\Store\Order\OrderItem;
use Symfony\Component\Routing\Router;

class CartItemDataAdapter implements ClientApplicationModelAdapterInterface
{
  private $router;
  private $stockManager;
  private $warehouseDataAdapter;
  private $productDataAdapter;

  public function __construct(Router $router, ProductStockManagerInterface $stockManager,
    WarehouseDataAdapter $warehouseDataAdapter, ProductDataAdapter $productDataAdapter)
  {
    $this->router = $router;
    $this->stockManager = $stockManager;
    $this->warehouseDataAdapter = $warehouseDataAdapter;
    $this->productDataAdapter = $productDataAdapter;
  }

  /**
   * @param $subject OrderItem
   * @param array $options
   * @return array
   */
  public function transform ($subject, $options = array())
  {
    $product = $subject->getProduct();
//    $warehouse = $this->stockManager->getAvailableWarehouse($product);

    return [
      'id' => $subject->getId(),
      'quantity' => $subject->getQuantity(),
      'price' => $subject->getPrice(),
      'product_id' => $subject->getPurchasableId(),
      'name' => $subject->getProduct()->getName(),
      'cost' => $subject->getCost(),
//      'warehouse' => $warehouse ? $this->warehouseDataAdapter->transform($warehouse): null,
      'product' => $product ? $this->productDataAdapter->transform($product) : null
    ];
  }

  public function getModelName ()
  {
    return 'OrderItem';
  }

  public function supports ($subject)
  {
    return $subject instanceof OrderItem;
  }

}