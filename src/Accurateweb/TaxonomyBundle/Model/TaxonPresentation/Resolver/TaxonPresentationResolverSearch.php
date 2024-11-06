<?php

namespace Accurateweb\TaxonomyBundle\Model\TaxonPresentation\Resolver;

use AccurateCommerce\Model\Taxonomy\TaxonInterface;
use AccurateCommerce\Sort\ProductSortFactoryInterface;
use Accurateweb\TaxonomyBundle\Model\Taxon\SearchTaxon;
use Accurateweb\TaxonomyBundle\Model\TaxonPresentation\Presentation\TaxonPresentationSearch;

class TaxonPresentationResolverSearch implements TaxonPresentationResolverInterface
{
  private $productSortFactory;

  public function __construct(ProductSortFactoryInterface $productSortFactory)
  {
    $this->productSortFactory = $productSortFactory;
  }

  public function resolve (TaxonInterface $taxon, array $options = [])
  {
    return new TaxonPresentationSearch($taxon, $options);
  }

  public function supports (TaxonInterface $taxon)
  {
    return $taxon instanceof SearchTaxon;
  }
}