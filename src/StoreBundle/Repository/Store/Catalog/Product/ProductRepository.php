<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Repository\Store\Catalog\Product;

use Doctrine\ORM\EntityRepository;
use StoreBundle\Entity\Store\Catalog\Taxonomy\Taxon;

class ProductRepository extends EntityRepository
{
  public function findBestOffers ()
  {
    return $this->createQueryBuilder('p')
      ->where('p.published = true')
      ->andWhere('p.hit = true')
      ->orderBy('p.rank', 'DESC')
      ->getQuery()
      ->getResult();
  }

  /**
   * Количество товаров, доступных для отображения в категории
   * @param Taxon $taxon
   * @return integer
   */
  public function countAvailableProductsByTaxon(Taxon $taxon)
  {
    return $this->getAvailableProductsByTaxonQuery($taxon)
      ->select('COUNT(p) as nbProducts')
      ->getQuery()
      ->setMaxResults(1)
      ->getSingleScalarResult();
  }

  /**
   * @param Taxon $taxon
   * @return mixed
   */
  public function findAvailableProductsByTaxon(Taxon $taxon)
  {
    return $this->getAvailableProductsByTaxonQuery($taxon)
      ->getQuery()
      ->getResult();
  }

  /**
   * @param Taxon $taxon
   * @return \Doctrine\ORM\QueryBuilder
   */
  public function getAvailableProductsByTaxonQuery(Taxon $taxon)
  {
    return $this->getAvailableProductsQuery()
      ->join('p.taxons', 't')
      ->andWhere('t.treeLeft >= :treeLeft')
      ->andWhere('t.treeRight <= :treeRight')
      ->setParameter('treeLeft', $taxon->getTreeLeft())
      ->setParameter('treeRight', $taxon->getTreeRight());
  }

  /**
   * @return \Doctrine\ORM\QueryBuilder
   */
  public function getAvailableProductsQuery()
  {
    return $this->createQueryBuilder('p')
      ->andWhere('p.published = true')
      ->andWhere('p.isPurchasable = true')
      ->andWhere('(p.totalStock - p.reservedStock) > 0');
//      ->orderBy('p.rank', 'DESC');
  }
}