<?php

namespace Accurateweb\MetaBundle\Resolver;

use Accurateweb\MetaBundle\Model\Meta;
use Accurateweb\MetaBundle\Model\MetaInterface;

class DefaultMetaResolver implements MetaResolverInterface
{
  private $meta;

  public function __construct ($title='', $description=null, $keywords=null)
  {
    $this->meta = new Meta();
    $this->meta
      ->setMetaDescription($description)
      ->setMetaKeywords($keywords)
      ->setMetaTitle($title);
  }

  public function getMeta ()
  {
    return $this->meta;
  }

  public function supports ()
  {
    return true;
  }
}