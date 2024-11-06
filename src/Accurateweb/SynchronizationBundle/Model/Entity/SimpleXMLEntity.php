<?php

namespace Accurateweb\SynchronizationBundle\Model\Entity;

use Accurateweb\SynchronizationBundle\Model\Entity\Base\BaseEntity;

class SimpleXMLEntity extends BaseEntity
{
  public function escapeSQLString($sql)
  {
    return (string) $sql;
  }

  public function parse($source, $parent = null)
  {
    $values = array();
    foreach ($source as $name => $value)
    {
      $values[$name] = (string)$value;
    }
    $this->setValues($values);
    $attributes = array();

    if ($source->attributes())
    {
      foreach ($source->attributes() as $name => $value)
      {
        $attributes[$name] = (string) $value;
      }
    }

    $this->setAttributes($attributes);
  }

  public function extractValues($values)
  {
    $extracted = array();
    foreach ($values as $name => $value)
    {
      $extracted[$name] = $value instanceof SimpleXMLElement ? (string)$value : $value;
    }

    return $extracted;
  }

}
