<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 09.02.18
 * Time: 10:51
 */

namespace StoreBundle\Repository\Store\Order\Status;


use Doctrine\ORM\EntityRepository;
use StoreBundle\Entity\Store\Order\Order;
use StoreBundle\Entity\Store\Order\Status\OrderStatus;

class OrderOrderStatusRepository extends EntityRepository
{

  /**
   * @param Order $order
   * @return OrderStatus|array
   */
  public function findLast(Order $order)
  {
    $all = $this->createQueryBuilder('os')
      ->where('os.order = :order')
      ->orderBy('os.createdAt', 'DESC')
      ->setParameter('order', $order)
      ->getQuery()
      ->getResult();

    if (count($all) === 0)
    {
      return $all;
    }
    else
    {
      return $all[0];
    }

  }

}