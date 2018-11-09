<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Model\Catalog\TaxonPresentation;


use AccurateCommerce\Model\Taxonomy\TaxonInterface;
use AccurateCommerce\Sort\ProductSortFactoryInterface;
use Accurateweb\SettingBundle\Model\Manager\SettingManagerInterface;
use Accurateweb\TaxonomyBundle\Model\TaxonPresentation\Presentation\TaxonPresentationProducts;
use StoreBundle\Model\Catalog\Filter\CheeseFilter;

/**
 * Представление для раздела с сырами, потому что в нем лютый кастомный фильтр
 *
 * @package StoreBundle\Model\Catalog\TaxonPresentation
 */
class TaxonPresentationCheese extends TaxonPresentationProducts
{
  private $settingManager;

  public function __construct(TaxonInterface $taxon, ProductSortFactoryInterface $sortFactory, array $options = [],
    SettingManagerInterface $settingManager)
  {
    $this->settingManager = $settingManager;

    parent::__construct($taxon, $sortFactory, $options);
  }

  protected function createProductFilter()
  {
    return new CheeseFilter('type', $this->getTaxon(), $this->settingManager);
  }
}