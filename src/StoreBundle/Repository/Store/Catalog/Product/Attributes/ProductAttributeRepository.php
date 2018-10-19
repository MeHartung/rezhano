<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 08.08.17
 * Time: 17:01
 */

namespace StoreBundle\Repository\Store\Catalog\Product\Attributes;

use Doctrine\ORM\EntityRepository;

class ProductAttributeRepository extends EntityRepository
{
  /**
   * Запрос для получения ТОЛЬКО атрибутов типа.
   * Т.к. product_attribute_id = id атрибута, то для запроса нам нужны только a.name (для label)
   * и a.id (для запроса)
   *
   * @param $productTypeId
   * @return \Doctrine\ORM\QueryBuilder
   */
  public function findProductAttributesForProductType($productTypeId)
  {
    $query =
      $this->createQueryBuilder('pa')
           ->innerJoin('StoreBundle:Store\Catalog\Product\Attributes\Type\ProductType', 'pt')
           ->where('pt.id = :id')
      ->setParameter('id', $productTypeId)
    ;

    return $query;
  }
}
