<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 08.08.17
 * Time: 17:02
 */

namespace StoreBundle\Repository\Store\Catalog\Product\Attributes;

use Doctrine\ORM\EntityRepository;

class ProductAttributeValueRepository extends EntityRepository
{
  public function getProductTypeAttributeValues($productAttributeId)
  {
    $result = $this->createQueryBuilder('w')
      ->select('d')
      ->from('StoreBundle:Store\Catalog\Product\Attributes\ProductAttributeValue', 'd')
      ->where('d.productAttribute = :productAttributeId')
      ->setParameter('productAttributeId', $productAttributeId);

    return $result;

  }
}