<?php

namespace Accurateweb\SynchronizationBundle\Model\Entity;

class EntityFactory
{
  private $className;

  public function __construct($className)
  {
/*    if ((!class_exists($className) || !array_search('BaseEntity', class_parents($className))))
    {
      throw new \InvalidArgumentException('$className must inherit from BaseEntity');
    }*/
    $this->className = $className;
  }

  public function create()
  {
    $className = $this->className;

    return new $className( );
  }

}
