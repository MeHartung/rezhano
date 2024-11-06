<?php

namespace Accurateweb\MetaBundle\Resolver;

use Accurateweb\MetaBundle\Model\MetaInterface;

interface MetaResolverInterface
{
  /**
   * @return MetaInterface
   */
  public function getMeta();

  /**
   * @return boolean
   */
  public function supports();
}