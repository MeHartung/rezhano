<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Model\Catalog\Filter\Field;

use AccurateCommerce\Store\Catalog\Filter\DoctrineChoiceFilterField;

class CheeseTypeFilterField extends DoctrineChoiceFilterField
{
  protected function evaluate($queryBuilder)
  {
    $choices =  [];

    $choices['uuu'] = "Мягкий";

    return $choices;
  }
}