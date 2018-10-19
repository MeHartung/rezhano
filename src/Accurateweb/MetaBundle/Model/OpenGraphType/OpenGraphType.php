<?php

namespace Accurateweb\MetaBundle\Model\OpenGraphType;

abstract class OpenGraphType
{
  /**
   * @return string
   */
  public abstract function getName();

  /**
   * @return OpenGraphTypeAttributeInterface[]
   */
  public abstract function getAttributes();

  public function __toString ()
  {
    return $this->getName();
  }
}