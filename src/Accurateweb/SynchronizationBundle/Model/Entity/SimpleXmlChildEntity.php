<?php

namespace Accurateweb\SynchronizationBundle\Model\Entity;

class SimpleXmlChildEntity extends SimpleXMLEntity
{

  public function parse($source, $parent = null)
  {
    parent::parse($source, $parent);
    $this->setValue('parent_id', (string) $parent->id);
  }

}
