<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Model\Catalog\TaxonPresentation;


use Accurateweb\TaxonomyBundle\Model\TaxonPresentation\Presentation\TaxonPresentationProducts;
use StoreBundle\Model\Catalog\Filter\CheeseFilter;

/**
 * Представление для раздела с сырами, потому что в нем лютый кастомный фильтр
 *
 * @package StoreBundle\Model\Catalog\TaxonPresentation
 */
class TaxonPresentationCheese extends TaxonPresentationProducts
{
  protected function createProductFilter()
  {
    return new CheeseFilter('type', $this->getTaxon());
  }
}