<?php

namespace Accurateweb\MetaBundle\Resolver;

use Accurateweb\MetaBundle\Model\MetaOpenGraphInterface;

interface MetaOpenGraphResolverInterface
{
  /**
   * @return MetaOpenGraphInterface
   */
  public function getMeta();

  /**
   * @return boolean
   */
  public function supports();
}