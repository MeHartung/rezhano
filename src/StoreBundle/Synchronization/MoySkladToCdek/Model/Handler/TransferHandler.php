<?php

namespace StoreBundle\Synchronization\MoySkladToCdek\Model\Handler;

use Accurateweb\SynchronizationBundle\Model\Handler\TransferHandler as BaseDataHandler;

class TransferHandler extends BaseDataHandler
{
  public function __construct($connection, $schema, $dispatcher, array $options = array())
  {
    parent::__construct($connection, $schema, $dispatcher, $options);
  }

  public function doTransfer()
  {
    $connection = $this->getConnection();
    $updateSql = $this->getTransferUpdateSql();
    
    if (false !== $updateSql && strlen($updateSql) > 0)
    {
      $this->query($this->processTemplate($updateSql), $connection);
    }
    
    $sql = $this->getTransferInsertSql();
    if (!empty($sql))
    {
      try
      {
        $this->query($this->processTemplate($sql), $connection);
      } catch (\Exception $exception)
      {
        return $exception;
      }
    }
  }
  
  
}