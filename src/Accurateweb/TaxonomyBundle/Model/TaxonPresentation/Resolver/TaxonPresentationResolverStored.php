<?php

namespace Accurateweb\TaxonomyBundle\Model\TaxonPresentation\Resolver;

use AccurateCommerce\Model\Taxonomy\TaxonInterface;
use AccurateCommerce\Sort\ProductSortFactoryInterface;
use Accurateweb\TaxonomyBundle\Model\TaxonPresentation\Presentation\TaxonPresentationChildSections;
use Accurateweb\TaxonomyBundle\Model\TaxonPresentation\Presentation\TaxonPresentationProducts;
use Accurateweb\TaxonomyBundle\Model\TaxonPresentation\TaxonPresentationInterface;
use StoreBundle\Entity\Store\Catalog\Taxonomy\Taxon;

/**
 * Предоставляет настройки представления для раздела
 *
 * @package Accurateweb\TaxonomyBundle\Model\TaxonPresentation\Resolver
 */
class TaxonPresentationResolverStored implements TaxonPresentationResolverInterface
{
  private $productSortFactory;

  public function __construct(ProductSortFactoryInterface $productSortFactory)
  {
    $this->productSortFactory = $productSortFactory;
  }

  public function resolve(TaxonInterface $taxon, array $options = array())
  {
    switch ($taxon->getTaxonEntity()->getPresentationId())
    {
      case TaxonPresentationInterface::TAXON_PRESENTATION_PRODUCTS:
        return new TaxonPresentationProducts($taxon, $this->productSortFactory, $options);
      case TaxonPresentationInterface::TAXON_PRESENTATION_CHILD_SECTIONS:
        return new TaxonPresentationChildSections($taxon, $options);
    }

    return new TaxonPresentationProducts($taxon, $this->productSortFactory, $options);
  }

  public function supports(TaxonInterface $taxon)
  {
    $taxonEntity = $taxon->getTaxonEntity();

    return $taxonEntity instanceof Taxon;
  }
}