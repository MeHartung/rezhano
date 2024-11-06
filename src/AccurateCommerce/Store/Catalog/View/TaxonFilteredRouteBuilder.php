<?php

namespace AccurateCommerce\Store\Catalog\View;

use AccurateCommerce\Store\Catalog\Filter\BaseFilter;
use StoreBundle\Entity\Store\Catalog\Taxonomy\Taxon;
use Symfony\Component\Routing\Router;

class TaxonFilteredRouteBuilder
{
  private $router;

  function __construct(Router $router)
  {
    $this->router = $router;
  }

  public function generate(Taxon $object, BaseFilter $filter = null, $parameters=array())
  {
    return sprintf("%s?%s",
      $this->router->generate('taxon', ['slug' => $object->getSlug()]),
      http_build_query(array_merge(
      $filter ? $filter->getParameters() : array(),
      $parameters)));
  }
}