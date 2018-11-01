<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Controller\Order;

use AccurateCommerce\DataAdapter\OrderClientApplicationAdapter;
use StoreBundle\DataAdapter\Logistic\InternalPickupPointAdapter;
use StoreBundle\Entity\Store\Order\Order;
use StoreBundle\Entity\Store\Shipping\PickupPoint;
use StoreBundle\Form\Checkout\CheckoutType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckoutController extends Controller
{
  public function checkoutAction(Request $request)
  {
    /** @var Order $order */
    /** @var Order $order */
    $order = $this->get('store.user.cart')->getCart();
    $items = $order->getOrderItems();
    
    if (!count($items))
    {
      if ($this->get('session')->getFlashBag()->has('order'))
      {
        /*
         * Если посетитель после оформления заказа не был отправлен на страницу complete, отправим его
         */
        $or = $this->get('session')->getFlashBag()->get('order');
        return $this->redirectToRoute('checkout_complete', [
          'documentNumber' => $or[0],
        ]);
      }
      
      return $this->render('@Store/Checkout/empty.html.twig');
    }
    
    $em = $this->getDoctrine()->getManager();
    
    $em->persist($order);
    $em->flush();
    
    $payment = $this->getDoctrine()->getRepository(\StoreBundle\Entity\Store\Payment\Method\PaymentMethod::class)->findOneBy(
      [
        'type' => '6cdec659-199f-43b6-ac05-f87a3a552f51',
        'enabled' => true
      ]
    );
    
    $order->setPaymentMethod($payment);
    $order->setShippingCityName('Екатеринбург');
    
    $form = $this->createForm(CheckoutType::class, $order);
    
    $form->handleRequest($request);
    
    if ($form->isSubmitted())
    {
      if ($form->isValid())
      {
        $order = $form->getData();
        
        $payment = $this->getDoctrine()->getRepository('StoreBundle:Store\Payment\Method\PaymentMethod')->find($order->getPaymentMethod()->getId());
        $order->setPaymentMethod($payment);
        
        /** @var $order Order */
        $this->get('store.checkout.processor')->process($order);
        
        $this->get('store.user.cart')->invalidateCart();
        $completedOrders = $this->get('session')->get('store.user.completed_orders');
        
        if (!$completedOrders)
        {
          $completedOrders = [];
        }
        
        $completedOrders[] = $order->getDocumentNumber();
        $this->get('session')->set('store.user.completed_orders', $completedOrders);
        
        return $this->redirectToRoute('checkout_complete', [
          'documentNumber' => $order->getDocumentNumber(),
        ]);
      }
    }
    
    return $this->render('@Store/Checkout/index.html.twig', array(
      'form' => $form->createView(),
      'items' => $items,
      'order' => $order
    ));
  }
  
  /**
   * Выдает список всех известных способов доставки
   *
   * @param Request $request Объект запроса
   * @return Response
   */
  public function shippingMethodListAction(Request $request)
  {
    $city = $request->get('city');
   /* $cart = $this->get('store.user.cart')->getCart();
    $postcode = $request->get('postcode');
    $location = $this->get('store.geography.location');
    
    if ($postcode !== null && $postcode !== $location->getCityPostcode())
    {
      $city = $location->getCityNameByPostcode($postcode);
      
      if (!$city)
      {
        $city = 'Екатеринбург';
      }
      
      $cart->setShippingCityName($city);
      $cart->setShippingPostCode($postcode);
    } else
    {
      $cart->setShippingCityName($location->getCityName());
      $cart->setShippingPostCode($location->getCityPostcode());
    }
    
    if ($request->get('postcode'))
    {
      $cart->setShippingPostCode($request->get('postcode'));
      $em = $this->getDoctrine()->getManager();
      $em->persist($cart);
      $em->flush();
    }
    
   */
   
    $cart = $this->get('store.user.cart')->getCart();
    $cart->setShippingCityName($city);
    $orderClientApplicationAdapter = new OrderClientApplicationAdapter($cart, $this->get('accurateweb.shipping.manager'));
   
    
    return new JsonResponse($orderClientApplicationAdapter->getShippingMethodClientModels()->toArray());
  }
  
  public function shippingChoiceListAction(Request $request)
  {
    $shippingManager = $this->get('accurateweb.shipping.manager');
    $shippingMethod = $shippingManager->getShippingMethodByUid($request->get('id'));
    
    if (!$shippingMethod)
    {
      throw $this->createNotFoundException(sprintf('Shipping method "%s" not found', $request->get('id')));
    }
    
    $location = $this->get('store.geography.location');
    $cart = $this->get('store.user.cart')->getCart();
    $cart->setShippingCityName($location->getCityName());
    
    if (!$cart->getShippingPostCode())
    {
      $cart->setShippingPostCode($location->getCityPostcode());
    }
    
    $orderClientApplicationAdapter = new OrderClientApplicationAdapter($cart, $shippingManager);
    $shipments = $orderClientApplicationAdapter->getCurrentShipments();
    
    return new JsonResponse($orderClientApplicationAdapter->getShippingChoiceList($shippingMethod, $shipments[0]));
  }
  
  /**
   * Возвращает доступные методы оплаты для заданных параметров
   *
   * @param Request $request
   * @return Response
   */
  public function paymentMethodListAction(Request $request)
  {
    $order = clone $this->get('store.user.cart')->getCart();
    
    $deliveryMethodId = $request->get('shipping_method_id');
    
    if ($deliveryMethodId)
    {
      $shippingMethod = $this->get('accurateweb.shipping.manager')->getShippingMethodByUid($deliveryMethodId);
      
     # $order->setShippingMethodEntity($shippingMethod);
    }
    
    $paymentMethodManager = $this->get('accuratecommerce.payment.method.manager');
    $paymentMethods = $paymentMethodManager->getAvailablePaymentMethods($order);
    $methods = [];
    
    foreach ($paymentMethods as $paymentMethod)
    {
      $methods[] = array(
        'id' => $paymentMethod->getId(),
        'name' => $paymentMethod->getName(),
        'enabled' => $paymentMethod->isEnabled(),
        'active' => $paymentMethod === $order->getPaymentMethod(),
        'help' => $paymentMethod->getDescription(),
        'fee' => $paymentMethodManager->calculateFee($order, $paymentMethod)
      );
    }
    
    return new JsonResponse($methods);
  }
  
  public function completeAction(Request $request, $documentNumber)
  {
    /**
     * Сюда мы попадаем с номер заказа, сформированным для корзины, которая помечена статусом CHECKOUT_STATE_CART_CHECKOUT
     * Оформленные заказы по этой корзине мы берем из сессии
     * Корзина нам нужна для полного списка товаров, а заказы для получения их номеров и сумм оплаты
     *
     * @TODO связать корзину и ее заказы в бд
     */
    /*   $orders = [];
       $completeOrders = $request->getSession()->get('store.user.orders.complete');
       $order = $this->getDoctrine()->getRepository(Order::class)->findOneBy(['documentNumber' => $documentNumber]);
   
       if (!$order || !$completeOrders || !is_array($completeOrders))
       {
         throw $this->createNotFoundException(sprintf('Заказ "%s" не найден', $documentNumber));
       }
   
       $currentUser = $this->getUser();
   
       if (!$currentUser || !$order->getUser() || $order->getUser()->getId() !== $currentUser->getId())
       {
         throw $this->createNotFoundException(sprintf('Заказ "%s" не найден для текущего пользователя', $documentNumber));
       }
   
       foreach ($completeOrders as $completeOrder)
       {
         $orders[] = $this->getDoctrine()->getRepository('StoreBundle:Store\Order\Order')->find($completeOrder);
       }
   
       $createDate = $order->getCheckoutAt();
   
       if (!$createDate || $createDate < new \DateTime('-10 minutes'))
       {
         throw $this->createNotFoundException(sprintf('Заказ "%s" слишком стар для просмотра', $documentNumber));
       }
   
       return $this->render('@Store/Checkout/complete.html.twig', [
         'cart' => $order,
         'orders' => $orders,
         'stockManager' => $this->get('aw.logistic.stock.manager'),
       ]);*/
    $order = $this->getDoctrine()->getRepository(Order::class)->findOneBy(['documentNumber' => $documentNumber]);
    
    if (!$order)
    {
      throw $this->createNotFoundException(sprintf('Заказ "%s" не найден', $documentNumber));
    }
    
    $completedOrders = $this->get('session')->get('store.user.completed_orders');
    
    if (!is_array($completedOrders) || !in_array($documentNumber, $completedOrders))
    {
      throw $this->createNotFoundException(sprintf('Заказ "%s" не был оформлен пользователем в рамках сессии', $documentNumber));
    }
    
    return $this->render('@Store/Checkout/complete.html.twig', [
      'order' => $order,
    ]);
  }
  
  public function pickupAction()
  {
    $pickups = $this->getDoctrine()->getRepository('StoreBundle:Store\Shipping\PickupPoint')->findAll();
    
    $pickupsJson = [];
    
    /** @var PickupPoint $pickup */
    foreach ($pickups as $pickup)
    {
      $adapter = new InternalPickupPointAdapter();
      $pickupsJson[] = $adapter->transform($pickup);
    }
    
    return new JsonResponse($pickupsJson);
    
  }
}