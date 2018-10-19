<?php

namespace Accurateweb\TaxonomyBundle\Model\TaxonPresentation\Renderer;

use Accurateweb\TaxonomyBundle\Model\TaxonPresentation\Presentation\TaxonPresentationSearch;
use Accurateweb\TaxonomyBundle\Model\TaxonPresentation\TaxonPresentationInterface;

class SearchTaxonPresentationRenderer implements TaxonPresentationRendererInterface
{
  private $twig;

  public function __construct (\Twig_Environment $twig)
  {
    $this->twig = $twig;
  }

  public function render (TaxonPresentationInterface $presentation, $options = null)
  {
    return $this->twig->render($presentation->getTemplateName(), [
      'presentation' => $presentation,
      'products' => $presentation->getProducts(),
      'pagination' => $presentation->getPagination(),
      'taxon' => $presentation->getTaxon(),
      'filter' => $presentation->getFilter(),
    ]);
  }

  public function supports (TaxonPresentationInterface $presentation)
  {
    return $presentation instanceof TaxonPresentationSearch;
  }

}