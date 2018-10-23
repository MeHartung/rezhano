<?php
/**
 * Created by PhpStorm.
 * User: eobuh
 * Date: 25.09.2018
 * Time: 19:11
 */

namespace Accurateweb\SynchronizationBundle\Model\Engine;


interface SynchronizationEngineInterface
{
  public function execute($subject, $direction, $local_filename, $options=array());
}