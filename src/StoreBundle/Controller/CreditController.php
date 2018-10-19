<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 15.11.17
 * Time: 14:00
 */

namespace StoreBundle\Controller;


use StoreBundle\Decorator\Order\OrderCreditDecorator;
use StoreBundle\Entity\Store\Order\Order;
use StoreBundle\Entity\Store\Order\OrderItem;
use StoreBundle\Service\Order\CreditService;
use StoreBundle\Validator\Constraints\PaymentMethod;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Отправляет заявки на кредиты.
 * Class CreditController
 *
 * @package StoreBundle\Controller
 */
class CreditController extends Controller
{
  /**
   * kupivkredit.ru (Tinkoff).
   */
  public function tinkoffCreditAction(Request $request)
  {
    $order = $this->getOrderByDocumentNumber($request->get('documentNumber'));
    
    if (!$order)
    {
      throw new NotFoundHttpException('Не найден заказ с таким номером.');
    }
    
    if ($order->getPaymentMethod()->getType() !== CreditService::TINKOFF_GUID)
    {
      return $this->redirect($this->generateUrl('checkout_complete', [
        'documentNumber' => $order->getDocumentNumber()
      ]));
    }
    
    $orderDecorator = new OrderCreditDecorator($order);
    $orderItems = $orderDecorator->getOrderItems();

    return $this->render('@Store/Checkout/kupiVCredit.html.twig', [
      'order' => $order,
      'orderItems' => $orderItems,
      'documentNumber' => $order->getDocumentNumber(),
      'form_action_url' => $this->getParameter('tinkoff_form_url'),
      'shopId' => $this->getParameter('tinkoff_shop_id'),
    ]);
  }
  
  public function alfaBankCreditAction(Request $request)
  {
    $order = $this->getOrderByDocumentNumber($request->get('documentNumber'));
    
    if (!$order)
    {
      throw new NotFoundHttpException('Не найден заказ с таким номером.');
    }
    
    if ($order->getPaymentMethod()->getType() !== CreditService::ALFA_BANK_GUID)
    {
      return $this->redirect($this->generateUrl('checkout_complete', ['documentNumber' => $order->getDocumentNumber()]));
    }
    
    $completedOrders = $this->get('session')->get('store.user.completed_orders');
    
    if (!is_array($completedOrders) || !in_array($order->getDocumentNumber(), $completedOrders))
    {
      throw $this->createNotFoundException(sprintf('Заказ "%s" не был оформлен пользователем в рамках сессии', $order->getDocumentNumber()));
    }
    
    $alfa = $this->get('store.user.credit')
      ->alfaBankXmlSend($order, $this->getParameter('alfa_bank_inn'), $this->getParameter('alfa_bank_url'));
    
    return $this->render('@Store/Checkout/alfaBankCredit.html.twig', [
      'alfa' => $alfa,
      'alfa_url' => $this->getParameter('alfa_bank_url'),
      'documentNumber' => $order->getDocumentNumber()
    ]);
  }
  
  private function getOrderByDocumentNumber($documentNumber)
  {
    $order = $this->getDoctrine()->getRepository(Order::class)
      ->findOneBy(['documentNumber' => $documentNumber]);
    
    return $order;
  }
}