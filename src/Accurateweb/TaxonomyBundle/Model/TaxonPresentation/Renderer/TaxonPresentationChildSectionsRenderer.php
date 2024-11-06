<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 12.09.2018
 * Time: 13:19
 */

namespace Accurateweb\TaxonomyBundle\Model\TaxonPresentation\Renderer;


use Accurateweb\TaxonomyBundle\Model\TaxonPresentation\Presentation\TaxonPresentationChildSections;
use Accurateweb\TaxonomyBundle\Model\TaxonPresentation\TaxonPresentationInterface;
use StoreBundle\Repository\Store\Catalog\Taxonomy\TaxonRepository;

class TaxonPresentationChildSectionsRenderer extends ProductListPresentationRenderer
{
  private $taxonRepository;

  public function __construct(\Twig_Environment $twig, TaxonRepository $taxonRepository)
  {
    $this->taxonRepository = $taxonRepository;

    parent::__construct($twig);
  }

  /**
   *
   * @param TaxonPresentationInterface $presentation
   * @param null $options
   * @return string
   * @throws \Twig_Error_Loader
   * @throws \Twig_Error_Runtime
   * @throws \Twig_Error_Syntax
   */
  public function render(TaxonPresentationInterface $presentation, $options = null)
  {
    $taxon = $presentation->getTaxon();

    $children = $taxon->getChildren();

    $childrenToDisplay = [];
    foreach($children as $child)
    {
      if ($this->taxonRepository->getTaxonHasProducts($child))
      {
        $childrenToDisplay[] = $child;
      }
    }

    $values = [
      'taxon' => $taxon,
      'children' => $childrenToDisplay
    ];

    return $this->getTwig()->render($presentation->getTemplateName(), $values);

  }

  public function supports(TaxonPresentationInterface $presentation)
  {
    return $presentation instanceof TaxonPresentationChildSections;
  }

}