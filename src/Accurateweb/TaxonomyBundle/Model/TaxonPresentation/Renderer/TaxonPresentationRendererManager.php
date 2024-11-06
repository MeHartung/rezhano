<?php

namespace Accurateweb\TaxonomyBundle\Model\TaxonPresentation\Renderer;

use Accurateweb\TaxonomyBundle\Exception\TaxonRendererNotFound;
use Accurateweb\TaxonomyBundle\Model\TaxonPresentation\TaxonPresentationInterface;

class TaxonPresentationRendererManager
{
  /**
   * @var TaxonPresentationRendererInterface[]
   */
  private $renderers;

  public function __construct ()
  {
    $this->renderers = [];
  }

  public function addRenderer(TaxonPresentationRendererInterface $renderer)
  {
    $this->renderers[] = $renderer;
  }

  /**
   * @param TaxonPresentationInterface $presentation
   * @param $options array|null
   * @return string
   * @throws TaxonRendererNotFound
   */
  public function render(TaxonPresentationInterface $presentation, $options=null)
  {
    foreach ($this->renderers as $renderer)
    {
      if ($renderer->supports($presentation))
      {
        return $renderer->render($presentation, $options);
      }
    }

    throw new TaxonRendererNotFound();
  }
}