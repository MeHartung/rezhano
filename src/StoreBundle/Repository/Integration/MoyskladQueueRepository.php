<?php

namespace StoreBundle\Repository\Integration;

use Doctrine\ORM\EntityRepository;
use StoreBundle\Entity\Store\Order\Order;

class MoyskladQueueRepository extends EntityRepository
{
  public function findNotSuccessfullySent()
  {
    return $this->createQueryBuilder('mq')
      ->join('mq.order', 'o')
      ->where('o.moyskladSent = 0')
      ->andWhere('o.checkoutStateId = :stateId')
      ->setParameter('stateId', Order::CHECKOUT_STATE_COMPLETE)
      ->getQuery()
      ->getResult();
  }
}