<?php

namespace Accurateweb\SynchronizationBundle\Model\Handler;

use Accurateweb\SynchronizationBundle\Model\Handler\Base\BaseDataHandler;

class InsertHandler extends BaseDataHandler #implements InsertHandlerInterface
{
  function insert(string $sql)
  {
    $con = $this->getConnection();
    $con->beginTransaction();
    $result = $this->query($sql, $con);

    if ($result)
    {
      $con->commit();
    }
    else
    {
      $con->rollback();
    }
  }

}
