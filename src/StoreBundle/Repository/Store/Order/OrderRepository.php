<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Repository\Store\Order;


use Deployer\Collection\PersistentCollection;
use Doctrine\ORM\EntityRepository;
use StoreBundle\Entity\Store\Order\Order;
use StoreBundle\Entity\Store\Order\Status\OrderStatus;
use StoreBundle\Entity\User\User;

class OrderRepository extends EntityRepository
{

  public function findAbandonedCarts($days)
  {
    $now = new \DateTime('today');

    $needleTime = $now->modify('-' . $days . ' days');

    return $this->createQueryBuilder('o')
      ->where('o.updatedAt <= :date')
      ->andWhere('o.checkoutStateId = :checkoutStateId')
      ->setParameter('date', $needleTime->format('Y-m-d'))
      ->setParameter('checkoutStateId', Order::CHECKOUT_STATE_CART)
      ->getQuery()->getResult();
  }

  /**
   * @param User $user
   * @return Order[]|PersistentCollection
   */
  public function findUserCompleteOrders(User $user)
  {
    return $this->getUserCompleteOrdersQueryBuilder($user)
      ->getQuery()
      ->getResult();
  }

  public function getUserCompleteOrdersQueryBuilder(User $user)
  {
    return $this->createQueryBuilder('o')
      ->where('o.user = :user')
      ->andWhere('o.checkoutStateId IN (:states)')
      ->orderBy('o.checkoutAt', 'DESC')
      ->setParameter('states', [Order::CHECKOUT_STATE_COMPLETE])
      ->setParameter('user', $user);
  }

  /**
   * @param $user
   * @return array
   */
  public function findUserActiveOrders($user)
  {
    $needleStatuses = $this->getEntityManager()->getRepository(OrderStatus::class)->getStatusChoices(true);
    $needleStatuses[] = null;

    return $this->findBy([
      'user' => $user,
      'orderStatus' => $needleStatuses,
      'checkoutStateId' => [Order::CHECKOUT_STATE_COMPLETE]
    ]);
  }

  /**
   * @param $user
   * @return array
   */
  public function findUserFinishedOrders($user)
  {
    $needleStatuses = $this->getEntityManager()->getRepository(OrderStatus::class)->getStatusChoices(false);

    return $this->findBy([
      'user' => $user,
      'orderStatus' => $needleStatuses,
      'checkoutStateId' => [Order::CHECKOUT_STATE_COMPLETE]
    ]);
  }

  /**
   * @param $user
   * @return int
   */
  public function countUserFinishedOrders($user)
  {
    $needleStatuses = $this->getEntityManager()->getRepository(OrderStatus::class)->getStatusChoices(false);

    return (int)$this->createQueryBuilder('o')
      ->select('count(o.id)')
      ->where('ost.isOrderActive = FALSE')
      ->andWhere('o.orderStatus IN :statuses')
      ->setParameter('user', $user)
      ->setParameter('statuses', $needleStatuses)
      ->getQuery()->getSingleScalarResult();

  }

}