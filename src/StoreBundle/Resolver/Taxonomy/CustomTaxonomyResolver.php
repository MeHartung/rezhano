<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 13.09.2018
 * Time: 19:10
 */

namespace StoreBundle\Resolver\Taxonomy;


use Accurateweb\TaxonomyBundle\Exception\TaxonNotFoundException;
use Accurateweb\TaxonomyBundle\Model\Resolver\TaxonomyResolverInterface;
use StoreBundle\Model\Catalog\Taxonomy\BestOffersTaxon;
use StoreBundle\Repository\Store\Catalog\Product\ProductRepository;

class CustomTaxonomyResolver implements TaxonomyResolverInterface
{
  private $taxonomyMap = [
   'best-offers' => BestOffersTaxon::class
  ];

  private $productRepository;

  public function __construct (ProductRepository $productRepository)
  {
    $this->productRepository = $productRepository;
  }

  public function resolve($slug)
  {
    try
    {
      $class = new \ReflectionClass($this->taxonomyMap[$slug]);
    }
    catch (\ReflectionException $e)
    {
      throw new TaxonNotFoundException(sprintf('Taxon "%s" is in the list of known custom taxons, but it\'s class is not found', $slug));
    }

    return $class->newInstance($this->productRepository);
  }

  public function supports($slug)
  {
    if (!is_string($slug))
    {
      return false;
    }

    return in_array($slug, array_keys($this->taxonomyMap));
  }

}