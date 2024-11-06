<?php

namespace Accurateweb\SynchronizationBundle\Model\Configuration;

use Accurateweb\SynchronizationBundle\Model\Datasource\DatasourceInterface;
use Accurateweb\SynchronizationBundle\Model\Datasource\DatasourceManagerInterface;
use Accurateweb\SynchronizationBundle\Model\Scenario\ScenarioManagerInterface;
use Accurateweb\SynchronizationBundle\Model\Scenario\SynchronizationScenarioInterface;
use Accurateweb\SynchronizationBundle\Model\Subject\SubjectManagerInterface;
use Accurateweb\SynchronizationBundle\Model\Subject\SynchronizationSubjectInterface;
use Accurateweb\SynchronizationBundle\Model\SynchronizationScenario;
use Accurateweb\SynchronizationBundle\Model\SynchronizationSubject;
use Doctrine\DBAL\Connection;
use Gedmo\Exception\InvalidArgumentException;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class SynchronizationServiceConfiguration implements SynchronizationConfigurationInterface
{

  private $datasources = array();
  private $dispatcher;
  private $subjects = array();
  private $scenarios = array();
  private $working_dir;
  private $sql_temp_dir;
  private $db_connection;
  private $kernelRootDir;
  
  private $scenarioManager, $subject, $datasource, $name;

  # connectionManager
  # subjectManager
  # scenariosManager
/*  public function __construct($dispatcher = null, $kernelRootDir = null)
  {
    $this->kernelRootDir = $kernelRootDir;
    $this->working_dir = str_replace('\\', '/', $this->kernelRootDir . '/../synchronization');
    $this->sql_temp_dir = $this->working_dir . '/sql';
    $this->dispatcher = $dispatcher;
    $this->setDbConnection($this->getDbConnection());
  } */
  
  /**
   * SynchronizationServiceConfiguration constructor.
   *
   * @param EventDispatcherInterface $dispatcher
   * @param Connection $connection
   * @param ScenarioManagerInterface $scenarioManager
   * @param SubjectManagerInterface $subject
   * @param DatasourceManagerInterface $datasource
   * @param $kernelRootDir string - не используется тип у параметра для совместимости с php 5.4
   */
  public function __construct(EventDispatcherInterface $dispatcher, Connection $connection,
                              SynchronizationSubjectInterface $subject,
                              DatasourceInterface $datasource, $kernelRootDir, $name)
  {
    $this->dispatcher = $dispatcher;
    
    $this->db_connection =   $connection;
   # $this->scenarioManager = $scenarioManager;
    $this->subject = $subject;
    $this->datasource = $datasource;
  
    $this->kernelRootDir = $kernelRootDir;
    $this->working_dir = str_replace('\\', '/', $this->kernelRootDir . '/../var/synchronization');
    $this->sql_temp_dir = $this->working_dir . '/sql';
    $this->name = $name;
  }

  public function getName()
  {
    return $this->name;
  }
  
  public function load($filename = null)
  {
/*    $finder = new Finder();
    $finder->files()->in($this->kernelRootDir."/config/")->name('parser.yml');

    foreach ($finder->files() as $file)
    {
      $filename = $file->getContents();
    }

    $configuration = Yaml::parse($filename);
    $this->loadConnections($configuration);
    $this->loadSubjects($configuration);
    $this->loadScenarios($configuration);*/
  }
  
  /**
   * @param $alias string
   * @return \Accurateweb\SynchronizationBundle\Model\Datasource\DatasourceInterface
   */
  public function getDatasource()
  {
    return $this->datasource;
  }

  private function loadConnections($configuration)
  {
    $this->datasources = array($this->datasource);

/*    $datasourceList = isset($configuration['datasources']) ? $configuration['datasources'] : array();
    foreach ($datasourceList as $name => $datasourceConfiguration)
    {

      if (!isset($datasourceConfiguration['class']))
      {
         throw new InvalidConfigurationException(sprintf('Не задан параметр class для соединения %s', $name));
      }

      $className = $datasourceConfiguration['class'];
      $options = array();

      if (isset($datasourceConfiguration['options']))
      {
        $options = array_merge($options, $datasourceConfiguration['options']);
      }
      
      $this->datasources[$name] = new $className($options);
    }*/
  }

  private function loadSubjects($configuration)
  {
    $subjects = (isset($configuration['subjects']) ? $configuration['subjects'] : array());
    foreach ($subjects as $name => $subjectConfiguration)
    {
      $this->subjects[$name] = new SynchronizationSubject($this, $name, $subjectConfiguration);
    }
  }

  private function loadScenarios($configuration)
  {
    /*$scenarioConfigurations = isset($configuration['scenarios']) ? $configuration['scenarios'] : array();
    foreach ($scenarioConfigurations as $scenarioName => $scenarioConfiguration)
    {

      $subjects = (isset($scenarioConfiguration['subjects']) ? $scenarioConfiguration['subjects'] : array());
      $scenarioClassName = isset($scenarioConfiguration['class']) ? $scenarioConfiguration['class'] : 'SynchronizationScenario';
      $scenario = new $scenarioClassName($this->dispatcher);
      if (!$scenario instanceof SynchronizationScenario)
      {
        throw new InvalidArgumentException('scenario must be an instance of SynchronizationScenario');
      }
      foreach ($subjects as $subjectName => $subjectConfiguration)
      {
        $scenario->addSubject($subjectName);
      }

      $this->scenarios[$scenarioName] = $scenario;
    }*/
  }

  public function getScenario($name)
  {
    return $this->scenarioManager->get($name);
  #  return isset($this->scenarios[$name]) ? $this->scenarios[$name] : null;
  }
  
  public function getScenarios()
  {
    return $this->scenarioManager->getAll();
  #  return isset($this->scenarios[$name]) ? $this->scenarios[$name] : null;
  }

  public function getSubject($name)
  {
    return $this->subject;
    #return isset($this->subjects[$name]) ? $this->subjects[$name] : null;
  }

  public function getSubjects()
  {
    return $this->subjects;
  }

  public function getParser()
  {
    return $this->subject->getParser();
  }

  public function getDefaultOf($subject, $name)
  {
    $result = null;
/*
    $subject = $this->getSubject($subject);

    if ($subject)
    {
      $result = $subject->getDefault($name);
    }*/

    return $result;
  }

  public function getWorkingDirectory()
  {
    return $this->working_dir;
  }

  public function getSqlTempDirectory()
  {
    return $this->sql_temp_dir;
  }

  public function setDbConnection($connection)
  {
    $this->db_connection = $connection;
  }

  public function getDbConnection()
  {
    return $this->db_connection;
  }

  function getInsertHandler($subject)
  {
    $handler = null;
    $schema = $this->getSchema($subject);
    $subject = $this->getSubject($subject);


    if ($subject)
    {
      $handler = $subject->getInsertHandler($this->db_connection, $schema, $this->dispatcher);
    }

    return $handler;
  }

  function getTransferHandler($subject)
  {
    $handler = null;

    $schema = $this->getSchema($subject);
    $subject = $this->getSubject($subject);

    if ($subject)
    {
      /** @var SynchronizationSubject $subject*/
      $handler = $subject->getTransferHandler($this->db_connection, $schema, $this->dispatcher);
    }


    return $handler;
  }

  function getSchema($subject)
  {
    $schema = null;
    $subject = $this->getSubject($subject);

    if ($subject)
    {
      $schema = $subject->getSchema();
    }

    return $schema;
  }

  public function getEventDispatcher()
  {
    return $this->dispatcher;
  }
}
