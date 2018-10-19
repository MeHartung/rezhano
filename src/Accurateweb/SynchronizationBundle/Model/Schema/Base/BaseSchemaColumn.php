<?php

namespace Accurateweb\SynchronizationBundle\Model\Schema\Base;

class BaseSchemaColumn
{
  private $name;
  private $type;
  private $size;
  private $mapWith;
  private $index;

  public function __construct($name)
  {
    $this->name = $name;
    $this->setIndex(false);
  }

  public function __toString()
  {
    $string = $this->name . ' ' . $this->type;

    if ($this->size)
    {
      $string .= '(' . $this->size . ')';
    }

    return $string;
  }

  public static function fromArray($values)
  {
    $result = new BaseSchemaColumn($values['name']);
    $result->setType($values['type']);

    if (isset($values['size']))
    {
      $result->setSize($values['size']);;
    }

    if (isset($values['mapWith']))
    {
      $result->setMapWith($values['mapWith']);
    };

    if (isset($values['index']))
    {
      $result->setIndex($values['index']);
    }

    return $result;
  }

  public function getMapWith()
  {
    return $this->mapWith;
  }

  public function getMappedColumn()
  {
    if (is_null($this->mapWith))
    {
      return $this->getName();
    }

    if ($this->mapWith === 'false')
    {
      return null;
    }

    return $this->mapWith;

  }

  public function getName()
  {
    return $this->name;
  }

  public function getSize()
  {
    return $this->size;
  }

  public function getType()
  {
    return $this->type;
  }

  public function setMapWith($v)
  {
    $this->mapWith = $v;
  }

  public function setSize($v)
  {
    $this->size = $v;
  }

  public function setType($v)
  {
    $this->type = $v;
  }

  public function getIndex()
  {
    return $this->index;
  }

  public function setIndex($v)
  {
    $this->index = (bool)$v;
  }
}
