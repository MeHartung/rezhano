<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Model\Catalog\Filter;

use AccurateCommerce\Store\Catalog\Filter\ProductFilter;
use StoreBundle\Model\Catalog\Filter\Field\CheeseTypeFilterField;

class CheeseFilter extends ProductFilter
{
  protected function configure()
  {
    $this->addField(new CheeseTypeFilterField('type', [
      'label' => 'Тип'
    ]));
    //$this->
  }
}