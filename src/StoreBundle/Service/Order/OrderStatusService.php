<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 09.02.18
 * Time: 10:43
 */

namespace StoreBundle\Service\Order;
use Doctrine\ORM\EntityManagerInterface;
use StoreBundle\Entity\Store\Order\Order;
use StoreBundle\Entity\Store\Order\Status\OrderStatusHistory;
use StoreBundle\Entity\Store\Order\Status\OrderStatus;
use StoreBundle\Entity\User\User;


/**
 * Обработка статусов заказа
 * Class OrderStatusService
 * @package StoreBundle\Service\Order
 */
class OrderStatusService
{
  /** @var EntityManagerInterface */
  private $em;

  /**
   * OrderStatusService constructor.
   * @param EntityManagerInterface $em
   */
  public function __construct(EntityManagerInterface $em)
  {
    $this->em = $em;
  }

  /**
   * @throws \Exception
   */
  public function setOrderStatus($data, Order $order, OrderStatus $orderStatus, User $user = null)
  {
    $orderStatusRepo = $this->em->getRepository(OrderStatusHistory::class);

    $last = $orderStatusRepo->findLast($order);

    /**
     * Если статус не меняли, но нажали в форме "Сохранить",
     * то значение всё рано отправится.
     * В таком случае никаких действий производить не надо.
     */
    if ($this->isDuplicate($data, $last))
    {
      return null;
    }

    $orderStatusHistory = new OrderStatusHistory();
    try
    {
      $order->setOrderStatus($orderStatus);
      $orderStatusHistory->setOrder($order);
      $orderStatusHistory->setStatus($orderStatus);
      $orderStatusHistory->setReason($data['reason']);
      $orderStatusHistory->setUser($user);

      $this->em->persist($order);
      $this->em->persist($orderStatusHistory);
      $this->em->flush();
    }
    catch (\Exception $e)
    {
      throw new \Exception($e->getMessage());
    }

    return $orderStatusHistory;
  }

  /**
   * @param $data
   * @param $last
   * @return bool
   */
  private function isDuplicate($data, $last)
  {
    if(!$last)
    {
      return false;
    }

    if($data['status'] === $last->getStatus()->getId() && $data['reason'] === $last->getReason())
    {
      return true;
    }

    return false;
  }


}