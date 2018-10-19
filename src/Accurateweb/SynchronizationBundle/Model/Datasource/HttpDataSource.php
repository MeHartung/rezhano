<?php

namespace Accurateweb\SynchronizationBundle\Model\Datasource;

use Accurateweb\SynchronizationBundle\Model\Datasource\Base\BaseDataSource;

class HttpDataSource extends BaseDataSource
{

  public function get($from, $to = null)
  {
    $fh = fopen($to, 'w');
    
    $ch = curl_init($from);
    
    curl_setopt($ch, CURLOPT_FILE, $fh);
    curl_exec($ch);
    curl_close($ch);
    
    fclose($fh);
    
    return $to;
  }

}
