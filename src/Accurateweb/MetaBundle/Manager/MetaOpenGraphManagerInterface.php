<?php

namespace Accurateweb\MetaBundle\Manager;

use Accurateweb\MetaBundle\Model\MetaOpenGraphInterface;

interface MetaOpenGraphManagerInterface
{
  /**
   * @return MetaOpenGraphInterface|null
   */
  public function getMetaOpenGraph();
}