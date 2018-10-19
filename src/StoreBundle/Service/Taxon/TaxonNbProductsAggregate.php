<?php

namespace StoreBundle\Service\Taxon;

use Doctrine\ORM\EntityManager;
use StoreBundle\Entity\Store\Catalog\Taxonomy\Taxon;

class TaxonNbProductsAggregate
{
  private $entityManager;
  private $productRepository;

  public function __construct (EntityManager $entityManager)
  {
    $this->entityManager = $entityManager;
    $this->productRepository = $this->entityManager->getRepository('StoreBundle:Store\Catalog\Product\Product');
  }

  public function recalculate(Taxon $taxon, $flush=true)
  {
    $nbProducts = $this->productRepository->countAvailableProductsByTaxon($taxon);
    $entities = [];

    if ($taxon->getNbProducts() != $nbProducts)
    {
      $taxon->setNbProducts($nbProducts);
      $this->entityManager->persist($taxon);
      $entities[] = $taxon;
    }

    if (count($entities) && $flush)
    {
      $this->entityManager->flush($entities);
    }
  }
}