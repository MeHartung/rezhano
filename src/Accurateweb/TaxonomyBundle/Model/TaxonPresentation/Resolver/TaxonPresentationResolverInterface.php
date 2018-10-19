<?php

namespace Accurateweb\TaxonomyBundle\Model\TaxonPresentation\Resolver;

use AccurateCommerce\Model\Taxonomy\TaxonInterface;
use Accurateweb\TaxonomyBundle\Model\TaxonPresentation\TaxonPresentationInterface;

interface TaxonPresentationResolverInterface
{
  /**
   * @param TaxonInterface $taxon
   * @return TaxonPresentationInterface
   */
  public function resolve(TaxonInterface $taxon, array $options = []);

  /**
   * @param TaxonInterface $taxon
   * @return boolean
   */
  public function supports(TaxonInterface $taxon);
}