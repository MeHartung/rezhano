<?php

namespace Accurateweb\MetaBundle\Manager;

use Accurateweb\MetaBundle\Model\MetaInterface;

interface MetaManagerInterface
{
  /**
   * @return MetaInterface|null
   */
  public function getMeta();
}