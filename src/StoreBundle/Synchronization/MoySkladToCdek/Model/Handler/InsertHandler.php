<?php

namespace AppBundle\Synchronization\MoySkladToCdek\Model\Handler;

use Accurateweb\SynchronizationBundle\Model\Handler\Base\BaseDataHandler;

class InsertHandler extends BaseDataHandler
{
  public function query($sql)
  {
    $stmt = $this->connection->prepare($sql);
    $result = $stmt->execute();
  
    if ($this->getOption('debug_sql') && $this->getOption('debug_profile'))
    {
      //$this->logger->info(sprintwf('Query finished', $sql));
    }
  
    if (!$result)
    {
      //$this->logger->addError(sprintf('Unable to execute query: %s...', $sql));
    }
  
    return $result;
  }
  
}
