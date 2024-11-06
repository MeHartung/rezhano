<?php

namespace Accurateweb\TaxonomyBundle\Model\TaxonPresentation\Presentation;

use AccurateCommerce\Pagination\Pagination;
use AccurateCommerce\Sort\ProductSort;
use AccurateCommerce\Store\Catalog\Filter\ProductFilter;
use Accurateweb\TaxonomyBundle\Model\Taxon\SearchTaxon;
use Accurateweb\TaxonomyBundle\Model\TaxonPresentation\TaxonFilterableInterface;
use Accurateweb\TaxonomyBundle\Model\TaxonPresentation\TaxonPaginationInterface;
use Accurateweb\TaxonomyBundle\Model\TaxonPresentation\TaxonPresentationInterface;
use Accurateweb\TaxonomyBundle\Model\TaxonPresentation\TaxonSortableInterface;

class TaxonPresentationSearch extends TaxonPresentationProducts
{
  private $taxon;
  private $pagination;
  private $sort;
  private $filter;

  public function getParameters ()
  {
    return [
      'showSubCategories' => true,
      'showFilter' => true
    ];
  }

  public function getTaxons()
  {
    return $this->taxon->getChildren();
  }

  public function getNbProducts()
  {
    return count($this->taxon->getProductSearch()->getObjectIds());
  }

  public function getNbTaxons()
  {
    return count($this->taxon->getCatalogSectionSearch()->getObjectIds());
  }
}