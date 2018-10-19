<?php

namespace Accurateweb\TaxonomyBundle\Model\Taxon;

use AccurateCommerce\Model\Taxonomy\TaxonInterface;
use StoreBundle\Entity\Store\Catalog\Taxonomy\Taxon;
use StoreBundle\Repository\Store\Catalog\Product\ProductRepository;
use StoreBundle\Repository\Store\Catalog\Taxonomy\TaxonRepository;

class TaxonFactory
{
  private $productRepository;
  private $taxonRepository;

  public function __construct (TaxonRepository $taxonRepository, ProductRepository $productRepository)
  {
    $this->productRepository = $productRepository;
    $this->taxonRepository = $taxonRepository;
  }

  /**
   * @param Taxon $taxonEntity
   * @return TaxonInterface
   */
  public function createStaticTaxon (Taxon $taxonEntity)
  {
    return new StaticTaxon($taxonEntity, $this->taxonRepository, $this->productRepository);
  }
}