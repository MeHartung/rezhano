<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Model\Catalog\Filter;

use AccurateCommerce\Model\Taxonomy\TaxonInterface;
use AccurateCommerce\Store\Catalog\Filter\EavFilter;
use AccurateCommerce\Store\Catalog\Filter\ProductFilter;
use Accurateweb\SettingBundle\Model\Manager\SettingManagerInterface;
use StoreBundle\Model\Catalog\Filter\Field\CheeseTypeFilterField;

class CheeseFilter extends ProductFilter
{
  private $settingsManager;

  private $eavFilter;

  public function __construct($id, TaxonInterface $taxon, SettingManagerInterface $settingManager,
    array $options = array())
  {
    $this->settingsManager = $settingManager;
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

    $qb = $this->taxon->getProductQueryBuilder();
    /**
     * @var $qb QueryBuilder
     */

    $productAttributes = $qb
      ->innerJoin('p.productAttributeValues', 'pav')
      ->innerJoin('pav.productAttribute', 'pa')
      ->select('pa.id', 'pa.name')
      ->where('pa.showInFilter = 1')
      ->orderBy('pa.name')
      ->groupBy('pa.id')
      ->getQuery()
      ->getArrayResult();

    $this->eavFilter = new EavFilter($productAttributes);

    foreach ($this->eavFilter->getFields() as $field)
    {
      $this->addField($field);
    }
  }
}