<?php

namespace AccurateCommerce\DataAdapter;

class ClientApplicationModelCollection implements \Iterator
{

  private $objects = array();

  public function append(ClientApplicationModelAdapterInterface $object)
  {
    $this->objects[] = $object;
  }

  public function isEmpty()
  {
    return empty($this->objects);
  }

  public function toArray($context = null)
  {
    $values = array();
    foreach ($this->objects as $object)
    {
      $values[] = $object->getClientModelValues($context);      
    }

    return $values;
  }
  
  /**
   * 
   * @param ClientApplicationModelAdapterInterface[] $models
   * @return ClientApplicationModelCollection
   */
  public static function createFromClientModelArray($models)
  {
    $collection = new ClientApplicationModelCollection();
    
    foreach ($models as $model)
    {
      $collection->append($model);
    }
    
    return $collection;
  }
  
  public function current()
  {
    return current($this->objects);
  }

  public function key()
  {
    return key($this->objects);
  }

  public function next()
  {
    return next($this->objects);
  }

  public function rewind()
  {
    return reset($this->objects);
  }

  public function valid()
  {
    return null !== key($this->objects);
  }

  /**
   * 
   * @param mixed $objects
   * @param String $adapterClass
   * 
   * @return ClientApplicationModelCollection
   */
  public static function createAdaptedCollection($objects, $adapterClass)
  {
    $collection = new ClientApplicationModelCollection();
    
    foreach ($objects as $model)
    {
      $collection->append(new $adapterClass($model));
    }
    
    return $collection;
  }

  public function serialize($context = null)
  {
    return sprintf('%s', json_encode($this->toArray($context)));
  }
}
