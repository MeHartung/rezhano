<?php

namespace Accurateweb\TaxonomyBundle\Model\Taxon;

use AccurateCommerce\Model\Taxonomy\TaxonInterface;
use Doctrine\ORM\QueryBuilder;
use StoreBundle\Entity\Store\Catalog\Taxonomy\Taxon;
use StoreBundle\Repository\Store\Catalog\Product\ProductRepository;
use StoreBundle\Repository\Store\Catalog\Taxonomy\TaxonRepository;

class StaticTaxon implements TaxonInterface
{
  private $taxonEntity;
  private $taxonRepository;
  private $productRepository;

  public function __construct (Taxon $taxonEntity, TaxonRepository $taxonRepository, ProductRepository $productRepository)
  {
    $this->taxonEntity = $taxonEntity;
    $this->taxonRepository = $taxonRepository;
    $this->productRepository = $productRepository;
  }

  /**
   * @inheritdoc
   */
  public function getProductQueryBuilder ($alias = 'p')
  {
    $qb = $this->createProductQueryBuilder($alias);
    $this->buildQuery($qb);
    return $qb;
  }

  /**
   * @inheritdoc
   */
  public function buildQuery (QueryBuilder $queryBuilder)
  {
    $queryBuilder->join('p.taxons', 't')
      ->andWhere($queryBuilder->expr()->gt('p.published', 0))
      ->andWhere($queryBuilder->expr()->gte('t.treeLeft', $this->taxonEntity->getTreeLeft()))
      ->andWhere($queryBuilder->expr()->lte('t.treeRight', $this->taxonEntity->getTreeRight()))
      ->andWhere('(p.totalStock - p.reservedStock) > 0')
      ->orderBy('p.isPurchasable', 'desc');
  }

  /**
   * @inheritdoc
   */
  public function getName()
  {
    return $this->taxonEntity->getName();
  }

  /**
   * @inheritdoc
   */
  public function getChildren ()
  {
    return $this->taxonRepository->getChildren($this->getTaxonEntity(), true);
  }

  /**
   * @inheritdoc
   */
  public function getTaxonEntity ()
  {
    return $this->taxonEntity;
  }

  /**
   * @param string $alias
   * @param null $indexBy
   * @return QueryBuilder
   */
  protected function createProductQueryBuilder($alias='p', $indexBy=null)
  {
    return $this->productRepository->createQueryBuilder($alias, $indexBy);
  }

  public function getId ()
  {
    return $this->taxonEntity->getId();
  }

  public function getShortName ()
  {
    return $this->getTaxonEntity()->getShortName();
  }

  public function getDescription ()
  {
    return $this->getTaxonEntity()->getDescription();
  }

  public function getSlug ()
  {
    return $this->getTaxonEntity()->getSlug();
  }

  public function getRouteName ()
  {
    return 'taxon';
  }

  public function getUrlParameters ()
  {
    return [
      'slug' => $this->getSlug()
    ];
  }

  public function getPresentationOptions()
  {
    return $this->getTaxonEntity()->getPresentationOptions();
  }


}