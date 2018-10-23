<?php
/**
 * Created by PhpStorm.
 * User: eobuh
 * Date: 25.09.2018
 * Time: 16:52
 */

namespace Accurateweb\SynchronizationBundle\Model\Datasource;


interface DatasourceManagerInterface
{
  /**
   * @param $alias string
   * @return DatasourceInterface
   */
 public function get($alias);
}