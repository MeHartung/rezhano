<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 05.02.18
 * Time: 18:00
 */

namespace StoreBundle\Repository\Store\Order\Status;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use StoreBundle\Entity\Store\Order\Status\OrderStatus;
use StoreBundle\Entity\Store\Order\Status\OrderStatusType;

class OrderStatusRepository extends EntityRepository
{

  /**
   * @param $active boolean
   * @return null|array
   */
  public function getStatusChoices($active = false)
  {
    $choices = [];

    $needleType = $this->getEntityManager()
      ->getRepository(OrderStatusType::class)
      ->findOneBy(['isOrderActive' => $active]);

    $_choices = $this->findBy(['type' => $needleType]);

    if($_choices)
    {
      foreach ($_choices as $_choice)
      {
        $choices[$_choice->getName()] = $_choice->getId();
      }
    }

    return $choices;

  }


}