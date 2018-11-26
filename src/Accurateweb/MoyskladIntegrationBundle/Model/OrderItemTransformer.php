<?php

namespace Accurateweb\MoyskladIntegrationBundle\Model;

use Accurateweb\MoyskladIntegrationBundle\Repository\MoyskladRepository;
use StoreBundle\Entity\Store\Order\OrderItem;

class OrderItemTransformer
{
  private $product_repository;

  public function __construct (MoyskladRepository $product_repository)
  {
    $this->product_repository = $product_repository;
  }

  public function transform(OrderItem $orderItem)
  {
    $product = $orderItem->getProduct();

    if (!$product)
    {
      throw new \Exception(sprintf('Product not linked to order item'));
    }

    $skald_code = $product->getExternalCode();

    if (!$skald_code)
    {
      throw new \Exception(sprintf('Product %s does not have a Moysklad code', $product->getId()));
    }

    $sklad_product = $this->product_repository->findOneBy(['code' => $skald_code]);

    if (!$sklad_product)
    {
      throw new \Exception(sprintf('Product %s not found in Moysklad with code %s', $product->getId(), $skald_code));
    }
    
    $sklad_product->quantity = (float)$orderItem->getQuantity();
    /*
     * Методом проб и ошибок пришел к тому, что они принимают копейки, а не рубли
     */
    $sklad_product->price = (float)$orderItem->getPrice()*100;

    return $sklad_product;
  }
}