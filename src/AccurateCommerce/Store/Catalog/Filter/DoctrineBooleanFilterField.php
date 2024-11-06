<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 25.09.2018
 * Time: 21:08
 */

namespace AccurateCommerce\Store\Catalog\Filter;


use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\QueryBuilder;

class DoctrineBooleanFilterField extends FilterField
{
  private $fieldName;

  public function __construct($fieldName, $id, array $options = array())
  {

    $this->fieldName = $fieldName;

    parent::__construct($id, $options);
  }

  /**
   * @param QueryBuilder $query
   */
  public function apply($query)
  {
    if ((bool)$this->getValue())
    {
      $query->andWhere(sprintf('p.%s > 0', $this->fieldName));
    }
  }

  protected function getWidgetId()
  {
    return 'checkbox';
  }

}