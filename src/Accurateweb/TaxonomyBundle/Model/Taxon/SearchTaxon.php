<?php

namespace Accurateweb\TaxonomyBundle\Model\Taxon;

use AccurateCommerce\Exception\SearchException;
use AccurateCommerce\Model\Taxonomy\TaxonInterface;
use AccurateCommerce\Search\CatalogSectionSearch;
use AccurateCommerce\Search\ProductSearch;
use Accurateweb\SphinxSearchBundle\Service\SphinxSearch;
use Doctrine\ORM\QueryBuilder;
use StoreBundle\Repository\Store\Catalog\Product\ProductRepository;
use StoreBundle\Repository\Store\Catalog\Taxonomy\TaxonRepository;

class SearchTaxon implements TaxonInterface
{
  private $taxonRepository;
  private $productSearch;
  private $catalogSectionSearch;
  private $productRepository;
  private $query;
  private $sphinxSearch;

  private $foundCatalogSections;

  public function __construct (TaxonRepository $taxonRepository, ProductRepository $productRepository, SphinxSearch $sphinxSearch, $query)
  {
    $this->taxonRepository = $taxonRepository;
    $this->productRepository = $productRepository;

    $this->sphinxSearch = $sphinxSearch;
    $this->query = $query;

    $this->productSearch = new ProductSearch($sphinxSearch->getSphinxClient(), $query);
    $this->catalogSectionSearch = new CatalogSectionSearch($sphinxSearch->getSphinxClient(), $query);

    $this->productSearch->execute();
    $this->catalogSectionSearch->execute();
  }

  public function getProductQueryBuilder ($alias = 'p')
  {
    $qb = $this->createProductQueryBuilder($alias);
    $this->buildQuery($qb);
    return $qb;
  }

  public function buildQuery (QueryBuilder $queryBuilder)
  {
    try
    {
      $this->productSearch->buildQuery($queryBuilder);
      $queryBuilder->select("p, field(p.id, " . implode(", ", $this->productSearch->getObjectIds()) . ") as HIDDEN relevanceseq");
    }
    catch (SearchException $e)
    {
      $queryBuilder->where('FALSE <> FALSE');
    }

    $queryBuilder->andWhere('(p.totalStock - p.reservedStock) > 0');

    return $queryBuilder;
  }

  public function getName ()
  {
    return sprintf('Результаты поиска по запросу &laquo;%s&raquo;', $this->query);
  }

  public function getChildren ()
  {
    return $this->getFoundCatalogSections();
  }

  public function getTaxonEntity ()
  {
    return null;
  }

  public function getId ()
  {
    return null;
  }

  public function getShortName ()
  {
    return $this->query;
  }

  public function getDescription ()
  {
    return sprintf('Результаты поиска по запросу &laquo;%s&raquo;', $this->query);
  }

  public function getSlug ()
  {
    return 'search';
  }

  public function getRouteName ()
  {
    return 'catalog_search';
  }

  public function getUrlParameters ()
  {
    return [
      'q' => $this->query,
    ];
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

  /**
   * @return QueryBuilder
   */
  protected function createTaxonQueryBuilder()
  {
    return $this->taxonRepository->createQueryBuilder('t');
  }

  /**
   * @return \Doctrine\Common\Collections\ArrayCollection
   */
  protected function getFoundCatalogSections()
  {
    if (null === $this->foundCatalogSections)
    {
      $this->foundCatalogSections = $this->catalogSectionSearch->getObjects($this->createTaxonQueryBuilder());
    }

    return $this->foundCatalogSections;
  }

  /**
   * @return ProductSearch
   */
  public function getProductSearch ()
  {
    return $this->productSearch;
  }

  /**
   * @return CatalogSectionSearch
   */
  public function getCatalogSectionSearch ()
  {
    return $this->catalogSectionSearch;
  }

  public function getPresentationOptions()
  {
    return [
      'showFilter' => true,
      'showSubCategories' => true
    ];
  }
}