<?php

namespace Accurateweb\TaxonomyBundle\Model\Taxon;


use Accurateweb\SphinxSearchBundle\Service\SphinxSearch;
use StoreBundle\Repository\Store\Catalog\Product\ProductRepository;
use StoreBundle\Repository\Store\Catalog\Taxonomy\TaxonRepository;

class SearchTaxonFactory
{
  private $taxonRepository;

  public function __construct (TaxonRepository $taxonRepository, ProductRepository $productRepository, SphinxSearch $sphinxSearch)
  {
    $this->taxonRepository = $taxonRepository;
    $this->productRepository = $productRepository;
    $this->sphinxSearch = $sphinxSearch;
  }

  /**
   * @param string $query
   * @return SearchTaxon
   */
  public function getTaxon($query)
  {
    return new SearchTaxon($this->taxonRepository, $this->productRepository, $this->sphinxSearch, $query);
  }
}