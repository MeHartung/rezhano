<?php

namespace Accurateweb\SynchronizationBundle\Model\Datasource;

use Accurateweb\SynchronizationBundle\Model\Datasource\Base\BaseDataSource;

class LocalDataSource extends BaseDataSource
{
  public function __construct(array $options = array())
  {
    parent::__construct($options);
  }
  

  public function get($from, $to = null)
  {
    return $from;
  }

  public function put($from, $to)
  {
    $data = file_get_contents($from);
    file_put_contents($to, $data);
  }

}
