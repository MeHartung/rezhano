<?php

namespace Accurateweb\TaxonomyBundle\Model\TaxonPresentation\Renderer;

use Accurateweb\TaxonomyBundle\Model\TaxonPresentation\TaxonPresentationInterface;

interface TaxonPresentationRendererInterface
{
  /**
   * @param TaxonPresentationInterface $presentation
   * @param array|null $options
   * @return string
   */
  public function render(TaxonPresentationInterface $presentation, $options=null);

  /**
   * @param TaxonPresentationInterface $presentation
   * @return boolean
   */
  public function supports(TaxonPresentationInterface $presentation);
}