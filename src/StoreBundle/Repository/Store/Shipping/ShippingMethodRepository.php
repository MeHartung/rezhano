<?php

namespace StoreBundle\Repository\Store\Shipping;

use AccurateCommerce\Shipping\Method\ShippingMethodUserDefined;
use Doctrine\ORM\EntityRepository;

class ShippingMethodRepository extends EntityRepository
{
  public function findNotCountryDelivery()
  {
    return $this->createQueryBuilder('sm')
      ->where('sm.uid = :uid')
      ->orderBy('sm.position')
      ->setParameter('uid', ShippingMethodUserDefined::UID)
      ->getQuery()->getResult();
  }
  
  public function findForRezhAndEkb()
  {
    return $this->createQueryBuilder('sm')
      ->where('sm.uid != :uid')
      ->orderBy('sm.position')
      ->setParameter('uid', ShippingMethodUserDefined::UID)
      ->getQuery()->getResult();
  }
}