<?php

namespace Accurateweb\SynchronizationBundle\Model\Datasource;

use Accurateweb\SynchronizationBundle\Model\Datasource\Base\BaseDataSource;

class StringDataSource extends BaseDataSource
{

  function get($from, $to = null)
  {
    if ($to === null)
    {
      $to = $this->getSavedName();
    }

    file_put_contents($to, $from);
    return $to;
  }

}
