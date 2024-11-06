<?php

namespace Accurateweb\SynchronizationBundle\Model\Configuration;


class SynchronizationConfigurationManager implements SynchronizationConfigurationManagerInterface
{
  /** @var SynchronizationConfigurationInterface[] */
  private $synchronizationConfigPool;
  
  public function __construct ()
  {
    $this->synchronizationConfigPool = array();
  }
  
  /**
   * @param $alias
   * @return SynchronizationConfigurationInterface
   */
  public function get($alias)
  {
    return $this->synchronizationConfigPool[$alias];
  }
  
  public function getAll()
  {
    return $this->synchronizationConfigPool;
  }
  
  public function addConfig (SynchronizationConfigurationInterface $configuration)
  {
    $alias = $configuration->getName();
    $this->synchronizationConfigPool[$alias] = $configuration;
  }
}