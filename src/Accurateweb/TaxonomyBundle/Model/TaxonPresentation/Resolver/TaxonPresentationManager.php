<?php

namespace Accurateweb\TaxonomyBundle\Model\TaxonPresentation\Resolver;

use AccurateCommerce\Model\Taxonomy\TaxonInterface;
use Accurateweb\TaxonomyBundle\Exception\TaxonPresentationNotFoundException;
use Accurateweb\TaxonomyBundle\Model\TaxonPresentation\TaxonPresentationInterface;

class TaxonPresentationManager
{
  /**
   * @var TaxonPresentationResolverInterface[]
   */
  private $resolvers;

  public function __construct ()
  {
    $this->resolvers = [];
  }

  /**
   * @param TaxonPresentationResolverInterface $resolver
   * @return $this
   */
  public function addResolver(TaxonPresentationResolverInterface $resolver)
  {
    $this->resolvers[] = $resolver;

    return $this;
  }

  /**
   * @param TaxonInterface $taxon
   * @return TaxonPresentationInterface|mixed
   * @throws TaxonPresentationNotFoundException
   */
  public function getTaxonPresentation(TaxonInterface $taxon, array $options = [])
  {
    foreach ($this->resolvers as $resolver)
    {
      if ($resolver->supports($taxon))
      {
        return $resolver->resolve($taxon, $options);
      }
    }

    throw new TaxonPresentationNotFoundException();
  }
}