<?php

namespace StoreBundle\Repository\Store\Shipping;

use AccurateCommerce\Shipping\Method\ShippingMethodUserDefined;
use Doctrine\ORM\EntityRepository;

class ShippingMethodRepository extends EntityRepository
{
  
  public function findNotCountryDelivery($city = null)
  {
    $qb = $this->createQueryBuilder('sm')
      ->where('sm.uid = :uid')
      ->setParameter('uid', ShippingMethodUserDefined::UID);
    
    if ($city !== null)
    {
      $qb->andWhere("sm.city = '$city'");
    }
    
    $qb->orderBy('sm.position');
    
    return $qb->getQuery()->getResult();
  }
  
  public function findForRezhAndEkb($city = '')
  {
    $qb = $this->createQueryBuilder('sm')
      ->where('sm.uid != :uid')
      ->setParameter('uid', ShippingMethodUserDefined::UID);
  
    if ($city !== null)
    {
      $qb->andWhere("sm.city = '$city'");
    }
  
    $qb->orderBy('sm.position');
  
    return $qb->getQuery()->getResult();
  }
}