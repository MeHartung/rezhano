<?php

namespace Accurateweb\SynchronizationBundle\Model\Handler\Base;

use Accurateweb\SynchronizationBundle\Model\Schema\Base\BaseSchema;
use Doctrine\DBAL\Connection;

class BaseDataHandler
{

  protected $schema = null;
  /** @var $connection Connection */
  protected $connection = null;
  protected $dispatcher = null;

  /**
   * Конструктор
   *
   * Список поддерживаемых опций:
   * - debug_sql boolean Указывает, нужно ли включать лог запросов к MySQL. По умолчанию false
   * - debug_profile boolean Указывает, нужно ли вести журнал операций. По умолчанию false
   *
   * @param $connection
   * @param $schema
   * @param $dispatcher
   * @param array $options Опции
   */
  public function __construct($connection, $schema, $dispatcher, $options = array())
  {
    $resolver = new \Symfony\Component\OptionsResolver\OptionsResolver();
    $this->configure($resolver);

    $this->schema = $schema;
    $this->connection = $connection;
    $this->dispatcher = $dispatcher;
    $this->options = $resolver->resolve($options);
  }

  protected function configure(\Symfony\Component\OptionsResolver\OptionsResolver $resolver)
  {
    $resolver->setDefault('debug_sql', false);
    $resolver->setDefault('debug_profile', false);
  }

  /**
   * Выполняет запрос к БД
   *
   * @param string $sql Текст SQL-запроса
   * @return mixed
   */
  public function query($sql)
  {
    if ($this->getOption('debug_sql'))
    {
    //  $this->logger->info(sprintf('SQL Query: %s', $sql));
    }

    $stmt = $this->connection->prepare($sql);
    $result = $stmt->execute();

    if ($this->getOption('debug_sql') && $this->getOption('debug_profile'))
    {
      //$this->logger->info(sprintf('Query finished', $sql));
    }

    if (!$result)
    {
      //$this->logger->addError(sprintf('Unable to execute query: %s...', $sql));
    }

    return $result;
  }

  public function getConnection()
  {
    return $this->connection;
  }

  function getOption($name)
  {
    return (isset($this->options[$name]) ? $this->options[$name] : null);
  }

  /**
   * Возвращает схему данных используемой таблицы
   * 
   * @return BaseSchema
   */
  function getSchema()
  {
    return $this->schema;
  }

}
