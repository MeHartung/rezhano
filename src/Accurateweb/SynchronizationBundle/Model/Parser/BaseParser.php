<?php

namespace Accurateweb\SynchronizationBundle\Model\Parser;

use Accurateweb\SynchronizationBundle\Exception\parserException;
use Accurateweb\SynchronizationBundle\Model\Configuration\SynchronizationServiceConfiguration;
use Accurateweb\SynchronizationBundle\Model\Entity\EntityCollection;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

abstract class BaseParser
{
  /** @var $entityFactory \Accurateweb\SynchronizationBundle\Model\Entity\EntityFactory */
  private $entityFactory;
  private $options;
  
  protected $entities;
  
  private $subject;
  private $serviceConfiguration;

  public function __construct($configuration, $subject, $entityFactory, $schema, $options)
  {
    $this->entityFactory = $entityFactory;
    $this->options = $options;
    
    $this->entities = new EntityCollection($schema);
    
    $this->subject = $subject;
    $this->serviceConfiguration = $configuration;
  }

  public function getEntities()
  {
    return $this->entities;
  }

  abstract protected function loadFile($filename);

  public function parseFile($filename)
  {
    $xml = $this->loadFile($filename);

    if ($xml === false)
    {
      throw new parserException('Unable to load source file');
    }

    return $this->parse($xml);
  }

  /**
   * @return mixed
   */
  protected function createEntity()
  {
    return $this->entityFactory->create();
  }

  public function getOption($name)
  {
    $value = null;

    if ($this->hasOption($name))
    {
      $value = $this->options[$name];
    }

    return $value;
  }

  /**
   * @return SynchronizationServiceConfiguration
   */
  protected function getServiceConfiguration()
  {
    return $this->serviceConfiguration;
  }

  protected function getSubject()
  {
    return $this->subject;
  }

  public function hasOption($name)
  {
    return isset($this->options[$name]);
  }

  abstract public function parse($source);

  abstract public function serialize($objects);

  public function fetchObjects()
  {
    $queryParameters = $this->getOption('query');

    if ((!empty($queryParameters) && isset($queryParameters['class'])))
    {
      $queryClass = $queryParameters['class'];
      $query = call_user_func($queryClass . '::create');

      if (!$queryClass)
      {
        throw new InvalidConfigurationException(sprintf('Unable to instantiate query class \'%s\'', $query));
      }

      $objects = $query->find();
      return $objects;
    }

    return array();
  }

}
