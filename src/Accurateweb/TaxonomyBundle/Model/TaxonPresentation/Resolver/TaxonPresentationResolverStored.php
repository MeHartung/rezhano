<?php

namespace Accurateweb\TaxonomyBundle\Model\TaxonPresentation\Resolver;

use AccurateCommerce\Model\Taxonomy\TaxonInterface;
use AccurateCommerce\Sort\ProductSortFactoryInterface;
use Accurateweb\SettingBundle\Model\Manager\SettingManagerInterface;
use Accurateweb\TaxonomyBundle\Model\TaxonPresentation\Presentation\TaxonPresentationChildSections;
use Accurateweb\TaxonomyBundle\Model\TaxonPresentation\Presentation\TaxonPresentationProducts;
use Accurateweb\TaxonomyBundle\Model\TaxonPresentation\TaxonPresentationInterface;
use StoreBundle\Entity\Store\Catalog\Taxonomy\Taxon;
use StoreBundle\Model\Catalog\TaxonPresentation\TaxonPresentationCheese;

/**
 * Предоставляет настройки представления для раздела
 *
 * @package Accurateweb\TaxonomyBundle\Model\TaxonPresentation\Resolver
 */
class TaxonPresentationResolverStored implements TaxonPresentationResolverInterface
{
  private $productSortFactory;

  private $settingManager;

  public function __construct(ProductSortFactoryInterface $productSortFactory,
    SettingManagerInterface $settingManager)
  {
    $this->productSortFactory = $productSortFactory;
    $this->settingManager = $settingManager;
  }

  public function resolve(TaxonInterface $taxon, array $options = array())
  {
    switch ($taxon->getTaxonEntity()->getPresentationId())
    {
      case TaxonPresentationInterface::TAXON_PRESENTATION_PRODUCTS:
        return new TaxonPresentationProducts($taxon, $this->productSortFactory, $options);
      case TaxonPresentationInterface::TAXON_PRESENTATION_CHILD_SECTIONS:
        return new TaxonPresentationChildSections($taxon, $options);
      case TaxonPresentationInterface::TAXON_PRESENTATION_CHEESE:
        return new TaxonPresentationCheese($taxon, $this->productSortFactory, $options, $this->settingManager);
    }

    return new TaxonPresentationProducts($taxon, $this->productSortFactory, $options);
  }

  public function supports(TaxonInterface $taxon)
  {
    $taxonEntity = $taxon->getTaxonEntity();

    return $taxonEntity instanceof Taxon;
  }
}