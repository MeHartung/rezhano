<?php

namespace Accurateweb\TaxonomyBundle\Model\Taxon;


use AccurateCommerce\Model\Taxonomy\TaxonInterface;
use Doctrine\ORM\QueryBuilder;
use StoreBundle\Entity\Store\Catalog\Taxonomy\Taxon;
use StoreBundle\Repository\Store\Catalog\Product\ProductRepository;

class FreeDeliveryTaxon implements TaxonInterface
{
  private $taxonEntity;
  private $productRepository;

  public function __construct (ProductRepository $productRepository)
  {
    $this->productRepository = $productRepository;
  }

  public function getProductQueryBuilder ($alias = 'p')
  {
    $qb = $this->createProductQueryBuilder($alias);
    $this->buildQuery($qb);

    return $qb;
  }

  public function buildQuery (QueryBuilder $queryBuilder)
  {
    $queryBuilder
      ->andWhere('p.is_free_delivery = 1')
      ->andWhere($queryBuilder->expr()->gt('p.published', 0))
      ->andWhere('(p.totalStock - p.reservedStock) > 0')
      ->orderBy('p.isPurchasable', 'desc');
  }

  public function getName ()
  {
    return 'Бесплатная доставка';
  }

  public function getChildren ()
  {
    return [];
  }

  public function getTaxonEntity ()
  {
    if (!$this->taxonEntity)
    {
      $this->taxonEntity = new Taxon();
      $this->taxonEntity
        ->setName($this->getName())
        ->setDescription($this->getDescription())
        ->setShortName($this->getShortName())
        ->setSlug($this->getSlug());
    }

    return $this->taxonEntity;
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

  public function getId ()
  {
    return null;
  }

  public function getShortName ()
  {
    return 'Бесплатная доставка';
  }

  public function getDescription ()
  {
    return '';
  }

  public function getSlug ()
  {
    return 'besplatnaya-dostavka';
  }

  public function getRouteName ()
  {
    return 'taxon';
  }

  public function getUrlParameters ()
  {
    return ['slug' => $this->getSlug()];
  }

  public function getPresentationOptions()
  {
    return [];
  }


}