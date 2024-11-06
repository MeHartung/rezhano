<?php
/**
 * Created by PhpStorm.
 * User: eobuh
 * Date: 25.09.2018
 * Time: 18:29
 */

namespace Accurateweb\SynchronizationBundle\Model\Connection;


interface ConnectionManagerInterface
{
  public function getConnection();
}