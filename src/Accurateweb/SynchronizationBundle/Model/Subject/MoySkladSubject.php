<?php

namespace Accurateweb\SynchronizationBundle\Model\Subject;

use Accurateweb\SynchronizationBundle\Model\Configuration\SynchronizationConfigurationInterface;
use Accurateweb\SynchronizationBundle\Model\Datasource\DatasourceInterface;
use Accurateweb\SynchronizationBundle\Model\Entity\SynchronizationEntityInterface;
use Accurateweb\SynchronizationBundle\Model\Handler\InsertHandlerInterface;
use Accurateweb\SynchronizationBundle\Model\Handler\TransferHandlerInterface;
use Accurateweb\SynchronizationBundle\Model\Parser\SynchronizationParserInterface;
use Accurateweb\SynchronizationBundle\Model\Schema\SynchronizationSchemaInterface;

class MoySkladSubject implements SynchronizationSubjectInterface
{
  protected $parser, $schema, $entity, $name, $datasource, $transferHandler, $insertHandler, $configuration;
  
  /**
   * MoySkladSubject constructor.
   *
   * @param SynchronizationParserInterface $parser
   * @param SynchronizationSchemaInterface $schema
   * @param SynchronizationEntityInterface $entity
   * @param $name string
   */
  public function __construct(SynchronizationParserInterface $parser, SynchronizationSchemaInterface $schema,
                              SynchronizationEntityInterface $entity, DatasourceInterface $datasource,
                              $insertHandler, TransferHandlerInterface $transferHandler,
                              /*SynchronizationConfigurationInterface $configuration,*/ $name)
  {
    $this->parser = $parser;
    $this->entity = $entity;
    $this->schema = $schema;
    $this->name = $name;
    $this->insertHandler = $insertHandler;
    $this->transferHandler = $transferHandler;
   # $this->configuration = $configuration;
  }
  
  /**
   * @return SynchronizationParserInterface
   */
  public function getParser(): SynchronizationParserInterface
  {
    return $this->parser;
  }
  
  /**
   * @return SynchronizationSchemaInterface
   */
  public function getSchema(): SynchronizationSchemaInterface
  {
    return $this->schema;
  }
  
  /**
   * @return SynchronizationEntityInterface
   */
  public function getEntity(): SynchronizationEntityInterface
  {
    return $this->entity;
  }
  
  /**
   * @return string
   */
  public function getName(): string
  {
    return $this->name;
  }
  
  public function getDatasource()
  {
    return $this->datasource;
  }
  
  /**
   * @return TransferHandlerInterface
   */
  public function getTransferHandler(): TransferHandlerInterface
  {
    return $this->transferHandler;
  }
  
  /**
   * @return InsertHandlerInterface
   */
  public function getInsertHandler()#: InsertHandlerInterface
  {
    return $this->insertHandler;
  }
  
  /**
   * @return SynchronizationConfigurationInterface
   */
  public function getConfiguration(): SynchronizationConfigurationInterface
  {
    return $this->configuration;
  }
  
}