<?php

namespace Accurateweb\SynchronizationBundle\Model\Handler;

use Accurateweb\SynchronizationBundle\Model\Handler\Base\BaseDataHandler;

class InsertHandler extends BaseDataHandler
{
  function insert($sql)
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
