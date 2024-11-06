<?php

namespace Accurateweb\TaxonomyBundle\Model\TaxonPresentation\Renderer;

use Accurateweb\TaxonomyBundle\Model\TaxonPresentation\Presentation\TaxonPresentationSearch;
use Accurateweb\TaxonomyBundle\Model\TaxonPresentation\TaxonFilterableInterface;
use Accurateweb\TaxonomyBundle\Model\TaxonPresentation\TaxonPaginationInterface;
use Accurateweb\TaxonomyBundle\Model\TaxonPresentation\TaxonPresentationInterface;
use Accurateweb\TaxonomyBundle\Model\TaxonPresentation\TaxonSortableInterface;
use StoreBundle\Entity\Store\Catalog\Taxonomy\Taxon;

class ProductListPresentationRenderer implements TaxonPresentationRendererInterface
{
  private $twig;

  public function __construct (\Twig_Environment $twig)
  {
    $this->twig = $twig;
  }

  public function render (TaxonPresentationInterface $presentation, $options=null)
  {
    if (!$options)
    {
      $options = [];
    }

    /** @var Taxon $taxonEntity */
    $taxonEntity = $presentation->getTaxon()->getTaxonEntity();

    $linkedTaxons = [];

    if ($taxonEntity instanceof Taxon)
    {
      $linkedTaxons = array_slice($taxonEntity->getLinkedTaxons()->toArray(), 0, 4);
    }

    $values = array_replace([
      'taxon' => $presentation->getTaxon(),
      'parameters' => $presentation->getParameters(),
      'products' => $presentation->getProducts(),
      'presentation' => $presentation,
      'linkedTaxons' => $linkedTaxons
    ], $options);

    if ($presentation instanceof TaxonSortableInterface)
    {
      $values['sort'] = $presentation->getSort();
    }

    if ($presentation instanceof TaxonPaginationInterface)
    {
      $values['pagination'] = $presentation->getPagination();
    }

    if ($presentation instanceof TaxonFilterableInterface)
    {
      $values['filter'] = $presentation->getFilter();
    }

    return $this->twig->render($presentation->getTemplateName(), $values);
  }

  public function supports (TaxonPresentationInterface $presentation)
  {
    return !($presentation instanceof TaxonPresentationSearch);
  }

  /**
   * Предоставлет доступ к Twig_Environment
   *
   * @return \Twig_Environment
   */
  protected function getTwig()
  {
    return $this->twig;
  }
}