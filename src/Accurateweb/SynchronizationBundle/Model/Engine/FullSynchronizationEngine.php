<?php

namespace Accurateweb\SynchronizationBundle\Model\Engine;

use Accurateweb\SynchronizationBundle\Model\Configuration\SynchronizationServiceConfiguration;
use Accurateweb\SynchronizationBundle\Model\Entity\EntityCollection;
use Accurateweb\SynchronizationBundle\Model\Handler\InsertHandler;
use Accurateweb\SynchronizationBundle\Model\Handler\TransferHandler;
use Accurateweb\SynchronizationBundle\Model\Parser\BaseParser;
use Accurateweb\SynchronizationBundle\Model\Subject\SynchronizationSubjectInterface;
use Accurateweb\SynchronizationBundle\Model\SynchronizationDirection;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class FullSynchronizationEngine extends BaseSynchronizationEngine
{
  private $dispatcher,
          $handlers = array(),
          $logger;

  /**
   *
   * @param SynchronizationServiceConfiguration $configuration
   */
  public function __construct($configuration)
  {
    parent::__construct($configuration);

    $this->dispatcher = $configuration->getEventDispatcher();
  }
  
  /**
   * @param $subject SynchronizationSubjectInterface
   * @param $direction
   * @param $local_filename
   * @param array $options
   * @throws \Exception
   */
  public function execute($subject, $direction, $local_filename, $options = array())
  {
    if ($direction != SynchronizationDirection::INCOMING)
    {
      throw new \InvalidArgumentException('Synchronization in this direction not implemented yet');
    }

    $this->handlers = array();
    /** @var BaseParser $parser */
    #$parser = $this->configuration->getParser($subject);
    $parser = $subject->getParser();

    if (!$parser)
    {
      throw new InvalidConfigurationException('Unable to instantiate parser class');
    }

  /*  $logger = asSynchronizationLogger::getInstance();
    if ($logger)
    {
      $logger->info(sprintf('Parsing: %s', $local_filename));
    }*/
    $entityCollections = $parser->getEntities($local_filename);
  
    if($entityCollections->count() < 1)
    {
     return'Нет объектов для синхронизации - в Моём складе не произошло изменений. ' . __DIR__ . '\\'.__CLASS__ . ':' . __LINE__;
      #throw new \Exception('Нет объектов для синхронизации - в Моём складе не произошло изменений. ' . __DIR__ . '\\'.__CLASS__ . ':' . __LINE__);
    }
    if (!is_array($entityCollections))
    {
      $entityCollections = array($subject->getName() => $entityCollections);
    }

    //$this->dispatcher->connect('entitycollection.limit', array($this, 'onEntityCollectionLimit'));
    try
    {
      foreach ($entityCollections as $collectionSubject => $entityCollection)
      {
        /* @var $entityCollection EntityCollection */
        //Настраиваем коллекции сущностей так, чтобы они оповещали нас о достижении ограничения на количество сущностей
        $entityCollection->setEventDispatcher($this->dispatcher);
        $entityCollection->setEntityLimit(500);
        $entityCollection->setSubjectName($collectionSubject);

        $sqlFileName = $this->getSqlFilename($collectionSubject);
    
        if (file_exists($sqlFileName))
        {
          file_put_contents($sqlFileName, '');
        }

        $sql = $entityCollection->getPreSql() . PHP_EOL;

        file_put_contents($sqlFileName, $sql, FILE_APPEND);
        /** @var InsertHandler $insertHandler */
        $insertHandler = $this->configuration->getInsertHandler($collectionSubject);
        $insertHandler->query($sql);

        $this->handlers[$collectionSubject] = array('insert' => $insertHandler);
      }
    
      #$parser->parseFile($local_filename);

      
      foreach ($entityCollections as $collectionSubject => $collectionEntities)
      {
        //Сброс последних распарсенных записей во временную таблицу
        //$this->dispatcher->notify(new sfEvent($collectionEntities, 'entitycollection.limit', array('subjectName' => $collectionSubject)));
  
        $this->onEntityLimit($collectionEntities, $collectionSubject);

        $transferHandler = $this->configuration->getTransferHandler($collectionSubject);

        /** @var $transferHandler TransferHandler */
        if (null !== $transferHandler)
        {
          $transferHandler->transfer();
        }
      }
  
  
  
      //  $this->dispatcher->disconnect('entitycollection.limit', array($this, 'onEntityCollectionLimit'));
    }
    catch (\Exception $e)
    {
    //  $this->dispatcher->disconnect('entitycollection.limit', array($this, 'onEntityCollectionLimit'));

      throw $e;
    }
  }

  /**
   * Обработчик события превышения ограничения количества разобранных сущностей в коллекции сущностей
   *
   * @param sfEvent $event Событие entitycollection.limit
   */
  public function onEntityCollectionLimit($event)
  {
    /** @var EntityCollection $collection */
    $collection = $event->getSubject();
    $subjectName = $event['subjectName'];

    $sql = $collection->toSQL();

    $sqlFileName = $this->getSqlFilename($subjectName);

    file_put_contents($sqlFileName, $sql, FILE_APPEND);

    $this->handlers[$subjectName]['insert']->query($sql);

    $collection->clear();
  }

  public function onEntityLimit(EntityCollection $event, $subjectName)
  {
    /** @var EntityCollection $collection */
    //$collection = $event->getSubject();

    $sql = $event->toSQL();

    $sqlFileName = $this->getSqlFilename($subjectName);

    file_put_contents($sqlFileName, $sql, FILE_APPEND);

    $this->handlers[$subjectName]['insert']->query($sql);

    //$collection->clear();
  }

  /**
   * Возвращает имя SQL-файла для дампа SQL-кода вставки во временную таблицц для заданного субъекта синхронизации
   *
   * @param String $subject Имя субъекта синхронизации
   * @return String
   *
   * @throws Exception
   */
  public function getSqlFilename($subject)
  {
    $sqlTempDir = $this->configuration->getSqlTempDirectory();

    if ((!is_dir($sqlTempDir) && !@mkdir($sqlTempDir, 0777, true)))
    {
      throw new \Exception('Unable to save temporary SQL data. Directory "%s" doesn\'t exist and couldn\'t be created', $sqlTempDir);
    }

    return sprintf('%s/%s.sql', $sqlTempDir, $subject);
  }
}
