<?php

namespace Accurateweb\SynchronizationBundle\Model\Handler;

use Accurateweb\SynchronizationBundle\Model\Handler\Base\BaseDataHandler;
use Accurateweb\SynchronizationBundle\Model\Schema\Base\BaseSchema;
use Symfony\Component\EventDispatcher\GenericEvent;

class TransferHandler extends BaseDataHandler implements TransferHandlerInterface
{
  private $keyIsNotNull;
  
  public function __construct($connection, $schema, $dispatcher, $options = array())
  {
    $this->keyIsNotNull = false;
    
    parent::__construct($connection, $schema, $dispatcher, $options);
  }

  protected function configure(\Symfony\Component\OptionsResolver\OptionsResolver $resolver)
  {
    parent::configure($resolver);

    $resolver->setDefault('key', 'id');
  }

  protected function preTransfer()
  {
    
  }

  public function transfer()
  {
/*    $logger = asSynchronizationLogger::getInstance();

    if ($logger)
    {
      $logger->info('Preparing transfer...');
    }*/
    $this->preTransfer();

/*    if ($logger)
    {
      $logger->info('Transferring...');
    }*/
    $this->doTransfer();
/*
    if ($logger)
    {
      $logger->info('Performing post-transfer operations...');
    }*/
    $this->postTransfer();
  }

  protected function postTransfer()
  {
    
  }

  protected function doTransfer()
  {
    $connection = $this->getConnection();
  //  $logger = asSynchronizationLogger::getInstance();

/*    if ($logger)
    {
      $logger->info('Updating existing records...');
    } */
    $updateSql = $this->getTransferUpdateSql();
    if (false !== $updateSql && strlen($updateSql) > 0)
    {
      $this->query($this->processTemplate($updateSql), $connection);
    }

   /* if ($logger)
    {
      $logger->info('Creating new records...');
    }*/
    $sql = $this->getTransferInsertSql();
    if (!empty($sql))
    {
        try{
            $this->query($this->processTemplate($sql), $connection);
        }catch (\Exception $exception)
        {
          $this->dispatcher->dispatch(
            'aw.sync.order_event.message',
            new GenericEvent((sprintf('%s.', $exception->getMessage())))
          );
        }
    }
    
  }

  protected function getTransferKeyField()
  {
    $key = $this->getOption('key');

    if (!$key)
    {
      $key = 'id';
    }


    return $key;
  }

  protected function getLocalTransferKeyField()
  {
    /** @var $schema BaseSchema */
    $key = $this->getTransferKeyField();
    $schema = $this->getSchema();

    if (!is_array($key))
    {
      $column = $schema->getColumn($key);

      return $column ? $column->getMappedColumn() : $key;
    }

    $compoundKey = array();
    foreach ($key as $entry)
    {
      $column = $schema->getColumn($entry);

      $compoundKey[] = $column ? $column->getMappedColumn() : $entry;
    }

    return $compoundKey;
  }

  protected function getTransferInsertSql()
  {
    $joinConditionSql = $this->getJoinConditionSql('t', 'o', ' AND ');
    $localKey = $this->getLocalTransferKeyField();

    if (!is_array($localKey))
    {
      $localKey = array($localKey);
    }

    $conditionSqlParts = array();
    foreach ($localKey as $localKeyPart)
    {
      $conditionSqlParts[] = sprintf('o.%s IS NULL', $localKeyPart);
    }
    $conditionSql = implode(' AND ', $conditionSqlParts);

    $transferKeyField = $this->getTransferKeyField();

    $groupBySqlParts = array();
    if (!is_array($transferKeyField))
    {
      $transferKeyField = array($transferKeyField);
    }
    foreach ($transferKeyField as $transferKeyFieldPart)
    {
      $groupBySqlParts[] = sprintf('t.%s', $transferKeyFieldPart);
    }

    $groupBySql = implode(', ', $groupBySqlParts);
    return ( 'INSERT INTO %table_name% ( %field_names% )
SELECT %t_field_names%
FROM %temp_table_name% t
LEFT JOIN %table_name% o
ON ' . $joinConditionSql . '
WHERE ' . $conditionSql . ';' );
  }/*return ( 'INSERT INTO %table_name% ( %field_names% )
SELECT %t_field_names%
FROM %temp_table_name% t
LEFT JOIN %table_name% o
ON ' . $joinConditionSql . '
WHERE ' . $conditionSql . '
GROUP BY ' . $groupBySql . ';' );
  }*/

  protected function getTransferUpdateSql()
  {
    $joinConditionSql = $this->getJoinConditionSql('src', 'dest', ' AND ');
    return <<<EOF
UPDATE %table_name%  dest, %temp_table_name% src SET
%field_map%
WHERE $joinConditionSql;
EOF;
  }

  protected function processTemplate($template)
  {
    $schema = $this->getSchema();
    $transferMap = $schema->getTransferMap();
    $field_map = array();
    $field_names = array();
    $t_field_names = array();

    foreach ($transferMap as $src => $dest)
    {
      if (false !== $dest)
      {
        $field_map[] = sprintf('dest.`%s`=src.`%s`', $dest, $src);
        $t_field_names[] = sprintf('t.`%s`', $src);
        $field_names[] = '`'.$dest.'`';
      }
    }

    $variables = array(
        '%table_name%' => $schema->getTableName(),
        '%temp_table_name%' => $schema->getTempTableName(),
        '%field_names%' => implode(', ', $field_names),
        '%t_field_names%' => implode(', ', $t_field_names),
        '%field_map%' => implode(',', $field_map)
    );

    if (!is_array($this->getTransferKeyField()))
    {
      $variables['%key%'] = $this->getTransferKeyField();
    }
    if (!is_array($this->getLocalTransferKeyField()))
    {
      $variables['%local_key%'] = $this->getLocalTransferKeyField();
    }

    $sql = $template;
    foreach ($variables as $name => $value)
    {
      $sql = str_replace($name, $value, $sql);
    }

    return $sql;
  }

  /**
   * Выполняет сопоставление записей таблиц с внешним ключом с записями в таблице, на которую этот ключ указывает, по 
   * некоторому идентификатору
   * 
   * @param String $tableName Название таблицы, записи которой будут обновляться сопоставленными внешними ключами
   * @param String $refTableName Название таблицы, содержащей сопоставляемые внешние ключи
   * @param String $localColumnName Название столбца с внешним ключом в таблице, записи которой будут обновляться сопоставленными внешними ключами
   * @param String $localRefColumnName Название столбца с ключом, по которому будет выполняться сопоставление в таблице, записи которой будут обновляться сопоставленными внешними ключами
   * @param String $remoteColumnName Название столбца, на который указывает внешний ключ в таблице, содержащей сопоставляемые внешние ключи
   * @param String $remoteRefColumnName Название столбца с ключом, по которому будет выполняться сопоставление в таблице, содержащей сопоставляемые внешние ключи
   * @param boolean $unbindBroken Если имеет значение true, несуществующие внешние ключи будут отвязаны
   */
  public function updateRelation($tableName, $refTableName, $localColumnName, $localRefColumnName, $remoteColumnName, $remoteRefColumnName, $unbindBroken = false)
  {
    $this->query(<<<EOF
UPDATE $tableName AS c, $refTableName AS p
SET c.$localColumnName = p.$localRefColumnName
WHERE c.$remoteColumnName = p.$remoteRefColumnName
EOF
    );

    if ($unbindBroken)
    {
      $this->query(<<<EOF
UPDATE $tableName AS ci
LEFT JOIN $refTableName cc ON (cc.$remoteRefColumnName = ci.$remoteColumnName)
SET ci.$localColumnName = NULL
WHERE cc.$localRefColumnName IS NULL
EOF
      );
    }
  }

  protected function getJoinConditionSql($remoteTableAlias, $localTableAlias, $glue = ', ')
  {
    $remoteKey = $this->getTransferKeyField();

    if (!is_array($remoteKey))
    {
      $remoteKey = array($remoteKey);
    }
    $localKey = $this->getLocalTransferKeyField();

    if (!is_array($localKey))
    {
      $localKey = array($localKey);
    }
    $conditionParts = array();
    foreach ($localKey as $i => $localKeyPart)
    {
      $remoteKeyPart = $remoteKey[$i];
      if ($this->keyIsNotNull)
      {
        $pattern = '(%s.%s = %s.%s)';
      }
      else
      {
        $pattern = '((%s.%s = %s.%s) OR (%1$s.%2$s IS NULL AND %3$s.%4$s IS NULL))';
      }
      $conditionParts[] = sprintf($pattern, $localTableAlias, $localKeyPart, $remoteTableAlias, $remoteKeyPart);
    }    

    return implode($glue, $conditionParts);
  }

  public function deleteMissingObjects()
  {
    $schema = $this->getSchema();
    $tableName = $schema->getTableName();
    $tempTableName = $schema->getTempTableName();
    $joinConditionSql = $this->getJoinConditionSql($tempTableName, $tableName, ' AND ');
    $keyMap = $this->getTransferKeyField();

    if (!is_array($keyMap))
    {
      $keyMap = array($keyMap);
    }
    $conditionParts = array();
    foreach ($keyMap as $key)
    {
      $conditionParts[] = sprintf('%s.%s IS NULL', $tempTableName, $key);
    }


    $condition = implode(' OR ', $conditionParts);
    $this->query(<<<EOF
DELETE $tableName FROM $tableName
LEFT JOIN $tempTableName ON ($joinConditionSql)
WHERE $condition
EOF
    );
  }

  /**
   * Устанавливает флаг ненулевого ключа. 
   * 
   * Флаг ненулевого ключа означает, что используемый при обновлении данных ключ не может содержать NULL в качестве значения
   * всего ключа или его части, если ключ составной
   * 
   * @param boolean $v
   */
  public function setKeyIsNotNull($v)
  {
    $this->keyIsNotNull = (bool)$v;
  }
  
  /**
   * 
   * @return boolean
   */
  public function getKeyIsNotNull()
  {
    return $this->keyIsNotNull;
  }
}
