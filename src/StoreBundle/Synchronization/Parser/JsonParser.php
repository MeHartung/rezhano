<?php

namespace StoreBundle\Synchronization\Parser;

use Accurateweb\SynchronizationBundle\Model\Entity\Base\BaseEntity;
use Accurateweb\SynchronizationBundle\Model\Parser\BaseParser;

class JsonParser extends BaseParser
{
  public function __construct($configuration, $subject, $entityFactory, $schema, $options)
  {
    parent::__construct($configuration, $subject, $entityFactory, $schema, $options);
  }
  
  public function loadFile($filename = '')
  {
    if(!$filename) return;
    
    $ordersAsString = file_get_contents($filename);
    $ordersAsArray = json_decode($ordersAsString, true);
    
    foreach ($ordersAsArray as $order)
    {
      /** @var BaseEntity $entity */
      $entity = $this->createEntity();
      $this->entities->add($entity->parse($order));
    }
    
    return $this;
  }
  
  public function serialize($objects)
  {
  
  }
  
  public function parse($source)
  {
    foreach ($source as $order)
    {
      /** @var BaseEntity $entity */
      $entity = $this->createEntity();
      $entity->parse($order);
      $this->entities->add($entity);
    }
  }
  
  public function getEntities($filename = null)
  {
    if(!$filename) return $this->entities;
  
    $ordersAsString = file_get_contents($filename);
    $ordersAsArray = json_decode($ordersAsString, true);
  
    foreach ($ordersAsArray as $order)
    {
      /** @var BaseEntity $entity */
      $entity = $this->createEntity();
      $entity->parse($order);
      $this->entities->add($entity);
    }

    return $this->entities;
  }
}