<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Controller\Order;

use AccurateCommerce\DataAdapter\OrderClientApplicationAdapter;
use AccurateCommerce\Shipping\Method\Store\ShippingMethodStorePickup;
use StoreBundle\DataAdapter\Logistic\InternalPickupPointAdapter;
use StoreBundle\Entity\Store\Order\Order;
use StoreBundle\Entity\Store\Shipping\PickupPoint;
use StoreBundle\Entity\Store\Shipping\ShippingMethod;
use StoreBundle\Form\Checkout\CheckoutType;
use StoreBundle\Validator\Constraints\PaymentMethod;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckoutController extends Controller
{
  public function deliveryStepAction(Request $request)
  {
    $cart = $this->get('store.user.cart')->getCart();
    $location = $this->get('store.geography.location');
    
    if (!$cart->getShippingCityName())
    {
      $cart->setShippingCityName($location->getCityName());
    }
    
    if (!$cart->getShippingPostCode())
    {
      $cart->setShippingPostCode($location->getCityPostcode());
    }
    
    if (!$cart->getOrderItems() || !count($cart->getOrderItems()))
    {
      if ($request->isXmlHttpRequest())
      {
        return new JsonResponse([
          'errors' => [
            '#' => 'Корзина пуста',
          ]
        ], 400);
      }
      
      return $this->redirectToRoute('cart_index');
    }
    
    $shipments = $cart->getShipments();
    $shipment = $shipments[0];
    $shippingManager = $this->get('accurateweb.shipping.manager');
    $shippingMethods = $shippingManager->getAvailableShippingMethodsForShipment($shipment);
    
    $pickupForm = $this->createForm('StoreBundle\Form\Checkout\Step\Delivery\PickupDeliveryStepType', $cart, [
      'shippingMethods' => $shippingMethods,
      'shippingMethodId' => ShippingMethodStorePickup::UID,
      'csrf_protection' => !$request->isXmlHttpRequest(),
    ]);
    $courierForm = $this->createForm('StoreBundle\Form\Checkout\Step\Delivery\CourierDeliveryStepType', $cart, [
      'shippingMethods' => $shippingMethods,
      'shippingMethodId' => ShippingMethodStoreCourier::UID,
      'csrf_protection' => !$request->isXmlHttpRequest(),
    ]);
    
    if ($request->isXmlHttpRequest())
    {
      $data = @json_decode($request->getContent(), true);
      
      if ($data && isset($data['shippingMethodId']))
      {
        switch ($data['shippingMethodId'])
        {
          case ShippingMethodStorePickup::UID:
            $pickupForm->submit($data);
            break;
          case ShippingMethodStoreCourier::UID:
            $courierForm->submit($data);
            break;
        }
      }
    } else
    {
      $pickupForm->handleRequest($request);
      $courierForm->handleRequest($request);
    }
    
    if ($pickupForm->isSubmitted() || $courierForm->isSubmitted())
    {
      $form = $pickupForm->isSubmitted() ? $pickupForm : $courierForm;
      
      if ($form->isValid())
      {
        /** @var Order $cart */
        $cart = $form->getData();
        
        $cart->setCheckoutStateId(Order::CHECKOUT_STATE_PAYMENT);
        $this->getDoctrine()->getManager()->persist($cart);
        $this->getDoctrine()->getManager()->flush();
        
        if ($request->isXmlHttpRequest())
        {
          return new JsonResponse([
            'cart' => $this->get('aw.client_application.transformer')->getClientModelData($cart, 'cart'),
          ], 200);
        }
        
        return $this->redirectToRoute('checkout_payment');
      }
      
      if ($request->isXmlHttpRequest())
      {
        return new JsonResponse([
          'errors' => $this->get('aw.client_application.transformer')->getClientModelData($form, 'form.error'),
        ], 400);
      }
    }
    
    $userCity = $this->getUser()->getCity();
    
    return $this->render('@Store/Checkout/steps/delivery.html.twig', [
      'city' => $userCity,
      'order' => $cart,
      'shippingMethods' => $shippingMethods,
      'currentStep' => Order::CHECKOUT_STATE_DELIVERY,
      'pickupForm' => $pickupForm->createView(),
      'courierForm' => $courierForm->createView(),
    ]);
  }
  
  public function paymentStepAction(Request $request)
  {
    $cart = $this->get('store.user.cart')->getCart();
    
    if (!$cart->getOrderItems() || !count($cart->getOrderItems()))
    {
      if ($request->isXmlHttpRequest())
      {
        return new JsonResponse(['errors' => [
          '#' => 'Корзина пуста'
        ]], 400);
      }
      
      return $this->redirectToRoute('cart_index');
    } elseif ($cart->getCheckoutStateId() < Order::CHECKOUT_STATE_DELIVERY)
    {
      if ($request->isXmlHttpRequest())
      {
        return new JsonResponse(['errors' => [
          '#' => 'Не пройден шаг выбора способа доставки'
        ]], 400);
      }
      
      $this->redirectToRoute('checkout_shipping');
    }
    
    $paymentMethodManager = $this->get('accuratecommerce.payment.method.manager');
    $paymentMethods = $paymentMethodManager->getAvailablePaymentMethods($cart);
    
    $form = $this->createForm('StoreBundle\Form\Checkout\Step\PaymentStepType', $cart, [
      'paymentMethods' => $paymentMethods,
      'csrf_protection' => !$request->isXmlHttpRequest(),
    ]);
    
    if ($request->isXmlHttpRequest())
    {
      $data = @json_decode($request->getContent());
      
      if ($data)
      {
        $form->submit($data);
      }
    } else
    {
      $form->handleRequest($request);
    }
    
    if ($form->isSubmitted())
    {
      if ($form->isValid())
      {
        /** @var Order $order */
        $order = $form->getData();
        
        /*
         * Оборачиваем в транзакцию, чтобы не плодить лишние заказы, в случае ошибки
         */
        $this->getDoctrine()->getConnection()->beginTransaction();
        
        try
        {
          /*
           * Делим корзину на заказы по городам
           */
          $ordersByCity = $this->get('store.cart_to_orders.converter')->convertToOrders($order);
          $orderIds = [];
          
          foreach ($ordersByCity as $item)
          {
            $this->get('store.checkout.processor')->process($item);
            $orderIds[] = $item->getId();
          }
          
          $request->getSession()->set('store.user.orders.complete', $orderIds);
          $order->setDocumentNumber($this->get('store.order.document_number_generator')->generate());
          $order->setCheckoutStateId(Order::CHECKOUT_STATE_CART_CHECKOUT);
          $order->setCheckoutAt(new \DateTime('now'));
          $this->getDoctrine()->getManager()->persist($order);
          $this->getDoctrine()->getManager()->flush();
          
          $this->getDoctrine()->getConnection()->commit();
          $this->get('store.user.cart')->invalidateCart();
          
          if ($request->isXmlHttpRequest())
          {
            return new JsonResponse([
              'documentNumber' => $order->getDocumentNumber(),
            ], 200);
          }
          
          return $this->redirectToRoute('checkout_complete', [
            'documentNumber' => $order->getDocumentNumber(),
          ]);
        } catch (\Exception $e)
        {
          $this->getDoctrine()->getConnection()->rollback();
          
          if ($request->isXmlHttpRequest())
          {
            return new JsonResponse([
              'errors' => [
                '#' => sprintf('Произошла ошибка оформления заказа. Подробности: %s', $e->getMessage()),
              ]
            ], 400);
          }
          
          $form->addError(new FormError(sprintf('Произошла ошибка оформления заказа. Подробности: %s', $e->getMessage())));
        }
      } else
      {
        if ($request->isXmlHttpRequest())
        {
          return new JsonResponse([
            'errors' => $this->get('aw.client_application.transformer')->getClientModelData($form, 'form.error'),
          ], 400);
        }
      }
    }
    
    return $this->render('@Store/Checkout/steps/payment.html.twig', [
      'paymentMethods' => $paymentMethods,
      'form' => $form->createView(),
      'currentStep' => Order::CHECKOUT_STATE_PAYMENT,
    ]);
  }
  
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
    
    $shipments = $order->getShipments();
    
    $form = $this->createForm(CheckoutType::class, $order/*, [
      'action' => $this->generateUrl('checkout'),
      'location' => $this->get('aw.location')->getLocation(),
   
      'city_repository' => $this->getDoctrine()->getRepository('StoreBundle:Store\Logistics\Delivery\Cdek\CdekCity'),
    ]*/);
    
    $form->handleRequest($request);
    
    if ($form->isSubmitted())
    {
      if ($form->isValid())
      {
        $order = $form->getData();
        
        $payment = $this->getDoctrine()->getRepository('StoreBundle:Store\Payment\Method\PaymentMethod')->find($order->getPaymentMethod()->getId());
        $order->setPaymentMethod($payment);
        
        /**
         * TODO выпилить это!1!
         */
        $shippingMethod = $this->getDoctrine()->getRepository(ShippingMethod::class)->findOneBy([
          'uid' => $order->getPaymentMethod()->getId()
        ]);
        
        #$order->setShippingMethodId($shippingMethod);
        #$order->setShippingMethod($shippingMethod);
        
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
        # $this->addFlash('order', $order->getDocumentNumber());
        
        return $this->redirectToRoute('checkout_complete', [
          'documentNumber' => $order->getDocumentNumber(),
        ]);
      }
    }
    
    # $countries = $this->getDoctrine()->getRepository('StoreBundle:Store\Logistics\Delivery\Cdek\CdekCountry')->findUsedCountries();
    #  $cities = $this->getDoctrine()->getRepository('StoreBundle:Store\Logistics\Delivery\Cdek\CdekCity')->findBy([
    #    'country' => $this->get('aw.location')->getLocation()->getCountryCode()
#    ], ['name' => 'ASC']);
    
    $countryCode = $this->get('aw.location')->getLocation()->getCountryCode();
    $cityName = $this->get('aw.location')->getLocation()->getCityName();
    
    #$country = $this->getDoctrine()->getRepository(CdekCountry::class)
    #  ->findOneBy(["id" => $countryCode]);
    
    # $city = $this->getDoctrine()->getRepository(CdekCity::class)
    #   ->findOneBy(['country' => $country, 'name' => $cityName]);
    
    # $duration = $this->get('app.shipping.service')->getDurationTime($city, $country);
    
    return $this->render('@Store/Checkout/index.html.twig', array(
      'form' => $form->createView(),
      'items' => $items,
      'order' => $order,
      #    'countries' => $countries,
      #   'cities' => $cities,
      #    'duration' => $duration,
    ));
    /**
     * TODO это от НВИ, удалить после того как стр. заработает
     */
    /*$order = $this->get('store.user.cart')->getCart();
    $items = $order->getOrderItems();

    if ($items->isEmpty())
    {
      return $this->redirectToRoute('cart_index');
    }

    switch ($order->getCheckoutStateId())
    {
      case Order::CHECKOUT_STATE_CART:
        return $this->redirectToRoute('cart_index');
      case Order::CHECKOUT_STATE_DELIVERY:
        return $this->redirectToRoute('checkout_delivery');
      case Order::CHECKOUT_STATE_PAYMENT:
        return $this->redirectToRoute('checkout_payment');
      case Order::CHECKOUT_STATE_COMPLETE:
        $this->get('store.user.cart')->invalidateCart();
        break;
    }

    return $this->redirectToRoute('cart_index');*/

//    $location = $this->get('store.geography.location');
//
//    $order->setShippingCityName($location->getCityName());
//    $order->setShippingPostCode($location->getCityPostcode());
//
//    $em = $this->getDoctrine()->getManager();
//
//    $em->persist($order);
//    $em->flush();
//
//    $form = $this->createForm(CheckoutType::class, $order, [
//      'action' => $this->generateUrl('checkout')
//    ]);
//    $form->handleRequest($request);
//
//    if ($form->isSubmitted() && $form->isValid())
//    {
//      $order = $form->getData();
//      /** @var $order Order */
//      $this->get('store.checkout.processor')->process($order);
//      $this->get('store.user.cart')->invalidateCart();
//
//      $completedOrders = $this->get('session')->get('store.user.completed_orders');
//
//      if (!$completedOrders)
//      {
//        $completedOrders = [];
//      }
//
//      $completedOrders[] = $order->getDocumentNumber();
//
//      $this->get('session')->set('store.user.completed_orders', $completedOrders);
//
//      return $this->redirectToRoute('checkout_complete', [
//        'documentNumber' => $order->getDocumentNumber(),
//      ]);
//    }
//
//    return $this->render('@Store/Checkout/index.html.twig', array(
//      'form' => $form->createView(),
//      'items' => $items
//    ));
  }
  
  /**
   * Выдает список всех известных способов доставки
   *
   * @param Request $request Объект запроса
   * @return Response
   */
  public function shippingMethodListAction(Request $request)
  {
    $cart = $this->get('store.user.cart')->getCart();
    $postcode = $request->get('postcode');
    /*
     * Установим город пользователя по умолчанию в соответствии с выбранным городом
     */
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
    
    $orderClientApplicationAdapter = new OrderClientApplicationAdapter($cart,
      $this->get('accurateweb.shipping.manager'));
    
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