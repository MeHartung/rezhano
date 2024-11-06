<?php

namespace StoreBundle\Synchronization\FromMoySklad\Model\Entity;

use Accurateweb\SynchronizationBundle\Model\Entity\Base\BaseEntity;

class JsonEntity extends BaseEntity
{
  /**
   * @param $source array - массив заказов
   * @param null $parent
   */
  public function parse($source, $parent = null)
  {
    $values = array();
    foreach ($source as $name => $value)
    {
      $values[$name] = (string) $value;
    }
    $this->setValues($values);
    $attributes = array();

    if(is_array($source)) return;
    if ($source->attributes())
    {
      foreach ($source->attributes() as $name => $value)
      {
        $attributes[$name] = (string) $value;
      }
    }
    
    $this->setAttributes($attributes);
  }
}