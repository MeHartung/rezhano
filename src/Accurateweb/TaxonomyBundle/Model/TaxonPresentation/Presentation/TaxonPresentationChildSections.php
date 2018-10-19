<?php

namespace Accurateweb\TaxonomyBundle\Model\TaxonPresentation\Presentation;

use AccurateCommerce\Model\Taxonomy\TaxonInterface;
use Accurateweb\TaxonomyBundle\Model\TaxonPresentation\ParameterContainerTrait;
use Accurateweb\TaxonomyBundle\Model\TaxonPresentation\TaxonPresentationInterface;

class TaxonPresentationChildSections implements TaxonPresentationInterface
{
  use ParameterContainerTrait;

  private $taxon;

  public function __construct (TaxonInterface $taxon, array $options = [])
  {
    $this->taxon = $taxon;
  }

  public function getTaxon ()
  {
    return $this->taxon;
  }

  public function getTemplateName ()
  {
    return '@Store/Catalog/Taxon/presentation/child_sections.html.twig';
  }

  public function prepare ()
  {

  }

  public function getProducts ()
  {
    return [];
  }
}