<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Model\Catalog\Filter;

use AccurateCommerce\Model\Taxonomy\TaxonInterface;
use AccurateCommerce\Store\Catalog\Filter\ProductFilter;
use Accurateweb\SettingBundle\Model\Manager\SettingManagerInterface;
use StoreBundle\Model\Catalog\Filter\Field\CheeseTypeFilterField;

class CheeseFilter extends ProductFilter
{
  private $settingsManager;

  public function __construct($id, TaxonInterface $taxon, SettingManagerInterface $settingManager,
    array $options = array())
  {
    $this->settingsManager = $settingManager;

    parent::__construct($id, $taxon, $options);
  }

  protected function configure()
  {
    $this->addField(new CheeseTypeFilterField('type', [
      'label' => 'Тип'
    ],
      $this->settingsManager
    ));
    //$this->
  }
}