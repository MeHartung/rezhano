<?php

namespace Accurateweb\TaxonomyBundle\Twig;

use Accurateweb\TaxonomyBundle\Model\TaxonPresentation\Renderer\TaxonPresentationRendererManager;
use Accurateweb\TaxonomyBundle\Model\TaxonPresentation\TaxonPresentationInterface;

class TwigRenderExtension extends \Twig_Extension
{
  private $presentationRendererManager;

  public function __construct (TaxonPresentationRendererManager $presentationRendererManager)
  {
    $this->presentationRendererManager = $presentationRendererManager;
  }

  public function getFunctions ()
  {
    return array(
      new \Twig_SimpleFunction('renderTaxonPresentation', [$this, 'renderTaxonPresentation'], [
        'is_safe' => ['html'],
      ]),
    );
  }

  public function renderTaxonPresentation(TaxonPresentationInterface $presentation, $options=null)
  {
    return $this->presentationRendererManager->render($presentation, $options);
  }
}