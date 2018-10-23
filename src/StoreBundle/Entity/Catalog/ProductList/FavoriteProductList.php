<?php

namespace StoreBundle\Entity\Catalog\ProductList;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use StoreBundle\Entity\User\User;

/**
 * @ORM\Entity
 */
class FavoriteProductList extends ProductList
{
  /**
   * @var User
   * @ORM\ManyToOne(targetEntity="StoreBundle\Entity\User\User")
   * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
   */
  protected $user;

  /**
   * @param null $limit
   * @return \StoreBundle\Entity\Store\Catalog\Product\Product[]|ArrayCollection
   */
  public function getProducts ($limit=null)
  {
    $products = parent::getProducts();

    if ($limit)
    {
      $criteria = Criteria::create()->setMaxResults($limit);
      $products = $products->matching($criteria);
    }

    return $products;
  }
}