<?php

namespace AppBundle\Controller;

use StoreBundle\Entity\User\User;
use StoreBundle\Exception\Catalog\RootNodeNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CommonController extends Controller
{
  public function homepageAction (Request $request)
  {
    $banners = $this->getDoctrine()->getRepository('AppBundle:Common\HomeBanner')
      ->findBy(['enabled' => true], ['position' => 'ASC']);

    $bestOffers = $this->getDoctrine()->getRepository('StoreBundle:Store\Catalog\Product\Product')
      ->findBy(['published' => true, 'isPurchasable' => true, 'hit' => true]);

    $viewedProducts = [];

    if ($this->getUser() instanceof User)
    {
      $viewedProducts = $this->getUser()->getViewedProductList()->getProducts(6);
    }

    return $this->render('@App/Common/index.html.twig', [
      'banners' => $banners,
      'bestOffers' => $bestOffers,
      'viewedProducts' => $viewedProducts,
    ]);
  }

  public function catalogMenuAction ()
  {
    $taxonRepository = $this->getDoctrine()->getRepository('StoreBundle:Store\Catalog\Taxonomy\Taxon');

    try
    {
      $root = $taxonRepository->getRootNode();
    }
    catch (RootNodeNotFoundException $e)
    {
      return new Response();
    }

    return $this->render('@App/Common/menu.html.twig', [
      'root' => $root,
      'repository' => $taxonRepository
    ]);
  }
}