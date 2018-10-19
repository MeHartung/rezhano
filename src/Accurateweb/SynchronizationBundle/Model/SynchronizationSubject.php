<?php

namespace Accurateweb\SynchronizationBundle\Model;

use Accurateweb\SynchronizationBundle\Model\Entity\EntityFactory;
use Symfony\Component\EventDispatcher\EventDispatcher;

class SynchronizationSubject
{

  const HANDLER_PARSER = 'parser';

  private $defaults = array();
  private $entityFactory;
  private $schema;
  private $handlers = array();
  private $name;
  private $options = array();

  public function __construct($serviceConfiguration, $name, $parameters)
  {
    $this->name = $name;

    if (isset($parameters['defaults']))
    {
      $this->defaults = array_merge($this->defaults, $parameters['defaults']);
    }
    if (isset($parameters['options']))
    {
      $this->options = array_merge($this->options, $parameters['options']);
    }

    if (isset($parameters['entity']))
    {
      $entityParams = $parameters['entity'];

      if (!isset($entityParams['class']))
      {
        throw new \Exception('Не задано имя класса для разбора сущностей');
      }
      $entityClass = $entityParams['class'];
      $this->entityFactory = new EntityFactory($entityClass);
    }
    
    $schemaClass = 'CommonSchema';
    $options = array();

    if (isset($parameters['schema']))
    {
      $schemaParams = $parameters['schema'];

      if (isset($schemaParams['class']))
      {
        $schemaClass = $schemaParams['class'];
      }
      if (isset($schemaParams['options']))
      {
        $options = $schemaParams['options'];
      }      
    }

    try
    {
      $this->schema = new $schemaClass($options);
    }
    catch (\Exception $e)
    {
      $serviceConfiguration->getEventDispatcher()->notify(new sfEvent($this, 'sync_service.error', array('message' => sprintf('Unable to instantiate schema for %s: "%s"', $this->name, $e->getMessage()))));

      throw new \Exception($e->getMessage(), $e->getCode(), $e);
    }

    if (isset($parameters["parser"]))
    {
      $parser_paremeters = $parameters["parser"];

      $class = $parser_paremeters["class"];
      if ($class !== false)
      {
        $options = array_merge(array(), isset($parser_paremeters["options"]) ? $parser_paremeters["options"] : array());

        $this->parser = new $class($serviceConfiguration, $this->name, $this->entityFactory, $this->schema, $options);
      }
    }
    else
      throw new \Exception("Parser configuration is missing. Make sure you've filled parser section in configuration file.");

    if (isset($parameters["insert"]))
    {
      $this->handlers["insert"] = $parameters["insert"];
    }

    if (isset($parameters["transfer"]))
    {
      $this->handlers["transfer"] = $parameters["transfer"];
    }
  }

  public function getParser()
  {
    return $this->parser;
  }

  public function getDefault($name)
  {
    return isset($this->defaults[$name]) ? $this->defaults[$name] : null;
  }

  public function getDefaults()
  {
    return $this->defaults;
  }

  public function getSchema()
  {
    return $this->schema;
  }

  public function getInsertHandler($connection, $schema, $dispatcher = null)
  {
    $className = ( isset($this->handlers['insert']) && isset($this->handlers['insert']['class']) ) ? $this->handlers['insert']['class'] : 'InsertHandler';
    $insertHandler = new $className($connection, $schema, $dispatcher, array());
    return $insertHandler;
  }

  public function getTransferHandler($connection, $schema, $dispatcher = null)
  {
    $className = (isset($this->handlers['transfer']) && isset($this->handlers['transfer']['class'])) ? $this->handlers['transfer']['class'] : 'TransferHandler';

    $options = ( isset($this->handlers['transfer']) && isset($this->handlers['transfer']['options']) ) ? $this->handlers['transfer']['options'] : array();
    $transferHandler = null;

    if (false !== $className)
    {
      $transferHandler = new $className($connection, $schema, $dispatcher, $options);
    }
    return $transferHandler;
  }

  public function getEntityFactory()
  {
    return $this->entityFactory;
  }

  public function getOption($name, $default = null)
  {
    return (isset($this->options[$name]) ? $this->options[$name] : $default);
  }

}
