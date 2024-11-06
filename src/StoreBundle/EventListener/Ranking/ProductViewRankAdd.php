<?php

namespace StoreBundle\EventListener\Ranking;


use Doctrine\ORM\EntityManager;
use StoreBundle\Entity\Store\Catalog\Product\Product;
use StoreBundle\Repository\Store\Catalog\Product\ProductRepository;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ProductViewRankAdd
{
  private $productRepository;
  private $entityManager;

  public function __construct (ProductRepository $productRepository, EntityManager $entityManager)
  {
    $this->productRepository = $productRepository;
    $this->entityManager = $entityManager;
  }

  public function onRequest(FilterControllerEvent $event)
  {
    if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST)
    {
      return;
    }

    $route = $event->getRequest()->get('_route');

    if ($route !== 'product')
    {
      return;
    }

    /** @var Product $product */
    $product = $this->productRepository->findOneBy(['slug' => $event->getRequest()->get('slug')]);

    if ($product)
    {
      $productRank = $product->getProductRank();
      $productRank->setNbViews($productRank->getNbViews() + 1);
      $this->entityManager->persist($product);
      $this->entityManager->flush();
    }
  }
}