<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 09.02.18
 * Time: 16:53
 */

namespace StoreBundle\Controller\Profile;

use AccurateCommerce\DataAdapter\ClientApplicationModelCollection;
use StoreBundle\DataAdapter\OrderHistoryDataAdapter;
use StoreBundle\Entity\Store\Order\Order;
use StoreBundle\Util\DateFormatter;
use StoreBundle\Voter\OrderVoter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UserOrderHistoryController extends Controller
{

  public function indexAction(Request $request)
  {
    $user = $this->getUser();
    $orderRepo = $this->getDoctrine()->getRepository(Order::class);

    return $this->render('@Store/Profile/order_history.html.twig',
      [
        'activeOrders' => $orderRepo->findUserActiveOrders($user),
        'finishedOrders' => $orderRepo->findUserFinishedOrders($user),
      ]);
  }

  /**
   * @param Request $request
   * Возращает информацию о конкретном заказе
   */
  public function orderInfoAction(Request $request)
  {
    $id = $request->get('id');
    $order = $this->getDoctrine()->getRepository(Order::class)->find($id);

    if (!$this->isGranted(OrderVoter::VIEW, $order))
    {
      return new JsonResponse('Order with id ' . $id . ' not found.', 404);
    }

    return new JsonResponse($this->get('aw.client_application.transformer')->getClientModelData($order, 'order'));
  }

  public function orderStatusesHistoryAction(Request $request, $orderId)
  {
    $order = $this->getDoctrine()->getRepository(Order::class)->find($orderId);

    if (!$order || !$this->isGranted(OrderVoter::VIEW, $order))
    {
      return new JsonResponse('Order with id ' . $orderId . ' not found.', 404);
    }

    $statuses = $order->getOrderStatusHistory();

    return new JsonResponse(
      $this->get('aw.client_application.transformer')->getClientModelCollectionData($statuses, 'order.status_history')
    );
  }
}