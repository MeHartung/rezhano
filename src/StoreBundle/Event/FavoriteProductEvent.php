<?php

namespace StoreBundle\Event;


use StoreBundle\Entity\Store\Catalog\Product\Product;
use StoreBundle\Entity\User\User;

class FavoriteProductEvent
{
  private $product;
  private $user;

  public function __construct (Product $product, User $user)
  {
    $this->product = $product;
    $this->user = $user;
  }

  /**
   * @return Product
   */
  public function getProduct (): Product
  {
    return $this->product;
  }

  /**
   * @return User
   */
  public function getUser (): User
  {
    return $this->user;
  }
}