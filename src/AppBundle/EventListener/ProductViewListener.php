<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\Catalog\ProductList\ProductListProduct;
use Doctrine\ORM\EntityManager;
use StoreBundle\Entity\User\User;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ProductViewListener
{
  private $tokenStorage;
  private $entityManager;

  public function __construct (TokenStorageInterface $tokenStorage, EntityManager $entityManager)
  {
    $this->tokenStorage = $tokenStorage;
    $this->entityManager = $entityManager;
  }

  public function onRequest(FilterControllerEvent $event)
  {
    $token = $this->tokenStorage->getToken();

    if (!$token)
    {
      return;
    }

    /** @var User $user */
    $user = $token->getUser();

    if (!$user instanceof User)
    {
      return;
    }

    if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST)
    {
      return;
    }

    $route = $event->getRequest()->get('_route');

    if ($route !== 'product')
    {
      return;
    }

    $list = $user->getViewedProductList();
    $product = $this->entityManager
      ->getRepository('StoreBundle:Store\Catalog\Product\Product')->findOneBy(['slug' => $event->getRequest()->get('slug')]);
    $productListProduct = $this->entityManager->getRepository('AppBundle:Catalog\ProductList\ProductListProduct')
      ->findOneBy(['productList' => $list, 'product' => $product]);

    if ($productListProduct)
    {
      $productListProduct->setCreatedAt(new \DateTime());
      $this->entityManager->persist($productListProduct);
    }
    else
    {
      $productListProduct = new ProductListProduct();
      $productListProduct->setProductList($list);
      $productListProduct->setProduct($product);

      $this->entityManager->persist($productListProduct);
    }

    $this->entityManager->flush();
  }
}