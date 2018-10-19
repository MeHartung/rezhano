<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Controller\Order;

use Accurateweb\LogisticBundle\Exception\StockableNotAvailableException;
use Doctrine\Common\Collections\ArrayCollection;
use StoreBundle\Entity\Store\Order\Order;
use StoreBundle\Entity\Store\Order\OrderItem;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CartController extends Controller
{
  public function addAction(Request $request)
  {
    $em = $this->getDoctrine()->getManager();

    $cartService = $this->get('store.user.cart');
    $cart = $cartService->getCart();

    $itemData = json_decode($request->getContent(), true);

    $itemForm = $this->createFormBuilder(null, ['csrf_protection' => false])
         ->add('product_id', IntegerType::class)
         ->add('quantity', IntegerType::class)
         ->getForm();

    /** @var $itemForm Form */
    $itemForm->submit($itemData);

    if (!$itemForm->isValid())
    {
      return new JsonResponse($itemForm->getErrors(true, true), 400);
    }

    $product = $this->getDoctrine()
                    ->getRepository('StoreBundle:Store\Catalog\Product\Product')
                    ->find($itemData['product_id']);

    if (!$product)
    {
      return new JsonResponse(['error' => 'Товар "%s" не найден'], 400);
    }

    $cartItem = $cartService->resolve($product);

    try
    {
      $this->get('aw.logistic.availability.manager')->validate($product, $cartItem->getQuantity() + $itemData['quantity']);
    }
    catch (StockableNotAvailableException $e)
    {
      return new JsonResponse(['error' => $e->getMessage()], 400);
    }

    $cartItem->setQuantity($cartItem->getQuantity() + $itemData['quantity']);

    $em->persist($cart);
    $em->flush();
    $em->refresh($cart);

    return $this->cartItemToJson($cartItem);
  }

  public function listAction()
  {
    $cart = $this->get('store.user.cart')->getCart();

    $items = [];
    foreach ($cart->getOrderItems() as $orderItem)
    {
      /** @var OrderItem $orderItem */
      $itemJson = $orderItem->toJSON();
      if (isset($itemJson['product']))
      {
        $itemJson['product']['url'] = $this->get('router')->generate('product', [ 'slug' => $orderItem->getProduct()->getSlug() ]);
      }

      $items[] = $itemJson;
    }

    return new JsonResponse($items);
  }

  public function removeAction($id)
  {
    $cart = $this->get('store.user.cart')->getCart();
    $em = $this->getDoctrine()->getManager();

    $orderItem = $this->getDoctrine()
                      ->getRepository('StoreBundle:Store\Order\OrderItem')
                      ->findOneBy(['id' => $id, 'order' => $cart]);

    if (!$orderItem)
    {
      throw $this->createNotFoundException(sprintf('Order item %d not found in user cart', $id));
    }

    $em->remove($orderItem);
    $em->flush();

    return new Response();
  }

  public function resetAction (Request $request)
  {
    $cart = $this->get('store.user.cart')->getCart();
    $em = $this->getDoctrine()->getManager();
    $orderItems = $cart->getOrderItems();

    if ($orderItems && count($orderItems))
    {
      foreach ($orderItems as $orderItem)
      {
        $em->remove($orderItem);
      }
    }

    $cart->setOrderItems(new ArrayCollection());
    $em->persist($cart);
    $em->flush();

    if ($request->isXmlHttpRequest())
    {
      return new JsonResponse();
    }

    return $this->redirectToRoute('cart_index');
  }

  /**
   * Страница корзины. Выводит содержимое корзины покупателя.
   */
  public function indexAction()
  {
    /** @var Order $order */
    $order = $this->get('store.user.cart')->getCart();
    $items = $order->getOrderItems();

    return $this->render('@Store/Cart/index.html.twig', [
      'order' => $order,
      'items' => $items,
      'currentStep' => Order::CHECKOUT_STATE_CART,
      'stockManager' => $this->get('aw.logistic.stock.manager'),
    ]);
  }

  public function updateAction(Request $request)
  {
    $em = $this->getDoctrine()->getManager();

    $cartItem = $em->getRepository('StoreBundle:Store\Order\OrderItem')
               ->find($request->get('id'));

    $cartService = $this->get('store.user.cart');

    if (!$cartService->canChange($cartItem))
    {
      return new JsonResponse(['error' => 'Cart item does not exist or is not changeable'], 404);
    }

    $cart = $cartService->getCart();
    $itemData = json_decode($request->getContent(), true);

//    $itemForm = $this->createFormBuilder(null, ['csrf_protection' => false])
//      ->add('quantity', IntegerType::class)
//      ->getForm();

    try
    {
      $this->get('aw.logistic.availability.manager')->validate($cartItem->getProduct(), $itemData['quantity']);
    }
    catch (StockableNotAvailableException $e)
    {
      return new JsonResponse(['error' => $e->getMessage()], 400);
    }

    $cartItem->setQuantity($itemData['quantity']);

    $em->persist($cart);
    $em->flush();

    return $this->cartItemToJson($cartItem);
  }

    /**
     * @param $cartItem
     * @return JsonResponse
     */
    public function cartItemToJson($cartItem)
    {
        $itemJson = $cartItem->toJSON();
        if (isset($itemJson['product'])) {
            $itemJson['product']['url'] = $this->get('router')->generate('product', ['slug' => $cartItem->getProduct()->getSlug()]);
        }

        return new JsonResponse($itemJson);
    }
}