<?php

namespace AppBundle\Entity\Catalog\ProductList;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use StoreBundle\Entity\User\User;

/**
 * @ORM\Entity
 */
class ViewedProductList extends ProductList
{
  /**
   * @var User
   * @ORM\ManyToOne(targetEntity="StoreBundle\Entity\User\User")
   * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
   */
  protected $user;

  /**
   * @param null $limit
   * @return \StoreBundle\Entity\Store\Catalog\Product\Product[]
   */
  public function getProducts ($limit=null)
  {
    $products = parent::getProducts();
    $criteria = Criteria::create();
    $expr = Criteria::expr();
    $criteria
      ->where($expr->eq('published', true))
      ->andWhere($expr->eq('purchasable', true))
      ->andWhere($expr->gt('availableStock', 0));

    if ($limit)
    {
      $criteria->setMaxResults($limit);
    }

    return $products->matching($criteria);
  }
}