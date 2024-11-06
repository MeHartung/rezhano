<?php

namespace Accurateweb\TaxonomyBundle\Model\Resolver;


use Accurateweb\TaxonomyBundle\Exception\TaxonNotFoundException;
use Accurateweb\TaxonomyBundle\Model\Taxon\TaxonFactory;
use StoreBundle\Repository\Store\Catalog\Taxonomy\TaxonRepository;

class RootCatalogResolver implements TaxonomyResolverInterface
{
  private $taxonFactory;
  private $repository;

  public function __construct (TaxonRepository $repository, TaxonFactory $taxonFactory)
  {
    $this->repository = $repository;
    $this->taxonFactory = $taxonFactory;
  }

  public function resolve ($criteria)
  {
    $root = $this->repository->getRootNode();

    if (!$root)
    {
      throw new TaxonNotFoundException();
    }

    return $this->taxonFactory->createStaticTaxon($root);
  }

  public function supports ($criteria)
  {
    return $criteria === 'root';
  }
}