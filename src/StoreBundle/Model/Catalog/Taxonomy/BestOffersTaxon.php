<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 13.09.2018
 * Time: 19:25
 */

namespace StoreBundle\Model\Catalog\Taxonomy;


use AccurateCommerce\Model\Taxonomy\TaxonInterface;
use Doctrine\ORM\QueryBuilder;
use StoreBundle\Repository\Store\Catalog\Product\ProductRepository;

/**
 * Раздел каталога "Лучшие предложения"
 *
 * @package StoreBundle\Model\Catalog\Taxonomy
 */
class BestOffersTaxon implements TaxonInterface
{
  private $productRepository;

  public function __construct (ProductRepository $productRepository)
  {
    $this->productRepository = $productRepository;
  }

  public function getProductQueryBuilder($alias = 'p')
  {
    $qb = $this->createProductQueryBuilder($alias);
    $this->buildQuery($qb);
    return $qb;
  }

  public function buildQuery(QueryBuilder $queryBuilder)
  {
    $queryBuilder
      ->andWhere('p.hit > 0')
      ->andWhere($queryBuilder->expr()->gt('p.published', 0))
      ->andWhere('(p.totalStock - p.reservedStock) > 0')
      ->orderBy('p.isPurchasable', 'desc');
  }

  public function getName()
  {
    return 'Лучшие предложения';
  }

  public function getChildren()
  {
    //У раздела "Лучшие предложения" нет дочерних разделов
    return [];
  }

  public function getTaxonEntity()
  {
    return null;
  }

  public function getId()
  {
    return null;
  }

  public function getShortName()
  {
    return 'Лучшие предложения';
  }

  public function getDescription()
  {
    return 'Лучшие предложения';
  }

  public function getSlug()
  {
    return 'best-offers';
  }

  public function getRouteName()
  {
    return 'taxon';
  }

  public function getUrlParameters()
  {
    return [
      'slug' => $this->getSlug()
    ];
  }

  /**
   * @param string $alias
   * @param null $indexBy
   * @return QueryBuilder
   */
  protected function createProductQueryBuilder($alias = 'p', $indexBy = null)
  {
    return $this->productRepository->createQueryBuilder($alias, $indexBy);
  }

  public function getPresentationOptions()
  {
    return [
      'showFilter' => true,
      'showSubCategories' => true
    ];
  }


}