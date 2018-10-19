<?php
/**
 * Created by PhpStorm.
 * User: eobuh
 * Date: 08.05.2018
 * Time: 8:39
 */

namespace Accurateweb\TaxonomyBundle\Model\Resolver;

use Accurateweb\TaxonomyBundle\Model\Taxon\FreeDeliveryTaxon;
use StoreBundle\Repository\Store\Catalog\Product\ProductRepository;

class FreeDeliveryTaxonResolver implements TaxonomyResolverInterface
{
  private $productRepository;

  public function __construct (ProductRepository $productRepository)
  {
    $this->productRepository = $productRepository;
  }

  public function resolve ($criteria)
  {
    return new FreeDeliveryTaxon($this->productRepository);
  }

  public function supports ($slug)
  {
    if (!is_string($slug))
    {
      return false;
    }

    return $slug == "besplatnaya-dostavka" ? true : false;
  }
}