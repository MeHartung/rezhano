<?php

/*
 * Автор Денис Н. Рагозин <dragozin at accurateweb.ru>
 */

namespace AccurateCommerce\Interop;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Exception;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Класс для запуска Doctrine в приложении
 *
 * @author Денис Н. Рагозин <dragozin at accurateweb.ru>
 */
class DoctrineAdapter
{
  /**
   * @var EntityManager 
   */
  private $entityManager;
  
  private static $instance = null;
  
  private function __clone() {}
  private function __construct($options)
  {
    $paths = array(dirname(__FILE__).'/../Resources/config/doctrine');    
    $isDevMode = true;

    $optionsResolver = new OptionsResolver();
    $optionsResolver->setDefault('db_driver', 'pdo_mysql');
    $optionsResolver->setDefault('charset', 'UTF8');
    $optionsResolver->setDefault('db_host', 'localhost');
    
    $optionsResolver->setRequired([
       'db_user', 'db_password', 'db_name'
    ]);
    
    $opts = $optionsResolver->resolve($options);
    
    // the connection configuration
    $dbParams = array(
      'driver'   => $opts['db_driver'],
      'user'     => $opts['db_user'],
      'password' => $opts['db_password'],
      'dbname'   => $opts['db_name'],
      'charset'  => $opts['charset'],
      'host'     => $opts['db_host'],
      'driverOptions' => array(
        1002 => 'SET NAMES utf8'
      )
    );

    $config = Setup::createYAMLMetadataConfiguration($paths, $isDevMode);
    $this->entityManager = EntityManager::create($dbParams, $config);
    
    $platform = $this->entityManager->getConnection()->getDatabasePlatform();
    $platform->registerDoctrineTypeMapping('enum', 'string');
  }
  
  /**
   * Создает экземпляр
   * 
   * @return DoctrineAdapter
   * @throws Exception
   */
  public static function createInstance($options)
  {
    if (null !== self::$instance)
    {
      throw new Exception();
    }

    self::$instance = new DoctrineAdapter($options);
    
    return self::$instance;
  }
  
  /**
   * Возвращает экземпляр класса
   * 
   * @return DoctrineAdapter
   */
  public static function getInstance()
  {
    return self::$instance;
  }
  
  /**
   * Возвращает Doctrine Entity Manager
   * 
   * @return EntityManager
   */
  public function getEntityManager()
  {
    return $this->entityManager;
  }
}
