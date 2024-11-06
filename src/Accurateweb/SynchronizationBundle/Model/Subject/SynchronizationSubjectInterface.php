<?php

namespace Accurateweb\SynchronizationBundle\Model\Subject;

use Accurateweb\SynchronizationBundle\Model\Configuration\SynchronizationConfigurationInterface;
use Accurateweb\SynchronizationBundle\Model\Datasource\DatasourceInterface;
use Accurateweb\SynchronizationBundle\Model\Entity\SynchronizationEntityInterface;
use Accurateweb\SynchronizationBundle\Model\Parser\SynchronizationParserInterface;
use Accurateweb\SynchronizationBundle\Model\Schema\SynchronizationSchemaInterface;

interface SynchronizationSubjectInterface
{
  /**
   * @return SynchronizationSchemaInterface
   */
  public function getSchema();
  
  /**
   * @return SynchronizationEntityInterface
   */
  public function getEntity();
  
  /**
   * @return SynchronizationParserInterface
   */
  public function getParser();
  
  /**
   * @return string
   */
  public function getName();
  
  /**
   * @return DatasourceInterface
   */
  public function getDataSource();
  
  /**
   * @return SynchronizationConfigurationInterface
   */
  public function getConfiguration();
}