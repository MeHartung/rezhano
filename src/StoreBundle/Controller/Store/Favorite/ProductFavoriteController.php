<?php

namespace StoreBundle\Controller\Store\Favorite;

use StoreBundle\Entity\Catalog\ProductList\FavoriteProductList;
use StoreBundle\Entity\User\User;
use StoreBundle\Event\FavoriteProductEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ProductFavoriteController extends Controller
{
  public function indexAction(Request $request)
  {
    /** @var FavoriteProductList $list */
    $list = $this->getUser()->getFavoriteProductList();

    return $this->render('StoreBundle:ProductFavorite:index.html.twig', [
      'products' => $list->getProducts(),
    ]);
  }

  public function toggleAction(Request $request, $productId)
  {
    $user = $this->getUser();

    if (!$user instanceof User)
    {
      throw new AccessDeniedHttpException();
    }

    $list = $user->getFavoriteProductList();
    $product = $this->getDoctrine()->getRepository('StoreBundle:Store\Catalog\Product\Product')->find($productId);

    if (!$product)
    {
      return new JsonResponse(['errors' => [
        '#' => sprintf('Товар %s не существует', $productId),
      ]], 400);
    }

    if ($list->getProducts()->contains($product))
    {
      $products = $list->getProductListProducts();

      foreach ($products as $prod)
      {
        if ($product->getId() === $prod->getProduct()->getId())
        {
          $this->getDoctrine()->getManager()->remove($prod);
        }

        $list->removeProduct($product);
      }
    }
    else
    {
      $list->addProduct($product);
      $this->get('event_dispatcher')->dispatch('store.favorite.product.add', new FavoriteProductEvent($product, $user));
    }

    $this->getDoctrine()->getManager()->persist($list);
    $this->getDoctrine()->getManager()->flush();

    if (!$request->isXmlHttpRequest())
    {
      return $this->redirectToRoute('favorites');
    }

    return new JsonResponse([]);
  }
}