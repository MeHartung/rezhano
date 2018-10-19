<?php
/**
 * Created by PhpStorm.
 * User: eobuh
 * Date: 08.05.2018
 * Time: 9:42
 */

namespace Accurateweb\TaxonomyBundle\Service;

use AccurateCommerce\Model\Taxonomy\TaxonInterface;
use Accurateweb\TaxonomyBundle\Exception\TaxonNotFoundException;
use Accurateweb\TaxonomyBundle\Model\Resolver\TaxonomyResolverInterface;
use StoreBundle\Entity\Store\Catalog\Taxonomy\Taxon;
use StoreBundle\Repository\Store\Catalog\Product\ProductRepository;

class TaxonomyManager
{
  /**
   * @var array
   */
  protected $resolvers;

  public function __construct ()
  {
    $this->resolvers = [];
  }

  /**
   * @param TaxonomyResolverInterface $resolver
   */
  public function addTaxonomyResolver (TaxonomyResolverInterface $resolver)
  {
    $this->resolvers[] = $resolver;
  }

  /**
   * @param $criteria mixed
   * @return null|TaxonInterface
   * @throws \Accurateweb\TaxonomyBundle\Exception\TaxonNotFoundException
   */
  public function getTaxon ($criteria)
  {
    $lastException = new TaxonNotFoundException();
    /** @var  $resolver TaxonomyResolverInterface */
    foreach ($this->resolvers as $resolver)
    {
      if ($resolver->supports($criteria))
      {
        try
        {
          $taxon = $resolver->resolve($criteria);
          return $taxon;
        }
        catch (TaxonNotFoundException $e)
        {
          $lastException = $e;
        }
      }
    }

    throw $lastException;
  }


}