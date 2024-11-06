<?php

namespace Accurateweb\TaxonomyBundle\Model\Resolver;

use AccurateCommerce\Model\Taxonomy\StaticTaxon;
use Accurateweb\TaxonomyBundle\Exception\TaxonNotFoundException;
use Accurateweb\TaxonomyBundle\Model\Taxon\TaxonFactory;
use StoreBundle\Entity\Store\Catalog\Taxonomy\Taxon;
use StoreBundle\Repository\Store\Catalog\Taxonomy\TaxonRepository;

class StaticTaxonResolver implements TaxonomyResolverInterface
{
  private $taxonRepository;
  private $taxonFactory;

  public function __construct (TaxonRepository $taxonRepository, TaxonFactory $taxonFactory)
  {
    $this->taxonRepository = $taxonRepository;
    $this->taxonFactory = $taxonFactory;
  }

  /**
   * @param mixed $slug
   * @return StaticTaxon|\AccurateCommerce\Model\Taxonomy\TaxonInterface
   * @throws TaxonNotFoundException
   */
  public function resolve ($slug)
  {
    /** @var Taxon $taxon */
    $taxon = $this->taxonRepository->findOneBy(['slug' => $slug]);

    if (!$taxon)
    {
      throw new TaxonNotFoundException(sprintf('Категория %s не найдена', $slug));
    }

    $staticTaxon = $this->taxonFactory->createStaticTaxon($taxon);

    $nbProducts = $staticTaxon->getProductQueryBuilder('p')
      ->select('COUNT(p)')
      ->getQuery()->getSingleScalarResult();

    if (!$nbProducts)
    {
      throw new TaxonNotFoundException(sprintf('Категория "%s" не содержит товаров', $slug));
    }

//    $taxonHasProducts = $this->taxonRepository->getTaxonHasProducts($taxon);
//
//    if (!$taxonHasProducts)
//    {
//      throw new TaxonNotFoundException(sprintf('Категория "%s" не содержит товаров', $slug));
//    }

    return $staticTaxon;
  }

  /**
   * @param mixed $criteria
   * @return bool
   */
  public function supports ($criteria)
  {
    if (!is_string($criteria))
    {
      return false;
    }

    return ($criteria !== "search") && ($criteria !== "besplatnaya-dostavka") ? true : false;
  }
}