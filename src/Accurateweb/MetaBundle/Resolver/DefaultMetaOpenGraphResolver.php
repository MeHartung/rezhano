<?php

namespace Accurateweb\MetaBundle\Resolver;

use Accurateweb\MetaBundle\Model\MetaOpenGraph;
use Accurateweb\MetaBundle\Model\OpenGraphType\OpenGraphTypeWebsite;

class DefaultMetaOpenGraphResolver implements MetaOpenGraphResolverInterface
{
  private $meta;

  public function __construct ($title='', $description=null, $siteName=null)
  {
    $this->meta = new MetaOpenGraph();
    $this->meta
      ->setTitle($title)
      ->setDescription($description)
      ->setSiteName($siteName)
      ->setImage('/images/logo.png')
      ->setType(new OpenGraphTypeWebsite())
      ->setLocale('ru-RU');
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