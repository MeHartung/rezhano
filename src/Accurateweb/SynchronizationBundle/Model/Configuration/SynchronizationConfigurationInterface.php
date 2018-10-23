<?php

namespace Accurateweb\SynchronizationBundle\Model\Configuration;


use Accurateweb\SynchronizationBundle\Model\Datasource\DatasourceInterface;

interface SynchronizationConfigurationInterface
{
  public function getName();
  
  /**
   * @return DatasourceInterface
   */
  public function getDatasource();
}