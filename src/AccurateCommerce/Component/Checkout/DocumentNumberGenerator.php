<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace AccurateCommerce\Component\Checkout;


use StoreBundle\Repository\Store\Order\OrderRepository;

class DocumentNumberGenerator
{
  private $orderRepository;

  public function __construct(OrderRepository $orderRepository)
  {
    $this->orderRepository  = $orderRepository;
  }

  public function generate()
  {
    $num = $this->orderRepository->createQueryBuilder('o')
        ->select('COUNT(o.id)')
        ->where("DATE(o.createdAt) = DATE(NOW()) AND o.checkoutStateId = 4")
        ->getQuery()
        ->getSingleScalarResult() + 1;

    $order_num = sprintf("%s-%d", date("ymd"), $num);
    while (count($this->orderRepository->findBy(['documentNumber' => $order_num])) > 0)
    {
      $order_num = sprintf("%s-%d", date("ymd"), ++$num);
    }

    return $order_num;
  }
}