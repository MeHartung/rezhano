<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 22.09.2017
 * Time: 17:28
 */

namespace StoreBundle\EventListener;


use AccurateCommerce\Shipping\ShippingManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use StoreBundle\Entity\Store\Order\Order;

class OrderShippingMethodInjectionListener
{
  private $shippingManager;

  public function __construct(ShippingManager $shippingManager)
  {
    $this->shippingManager = $shippingManager;
  }

  public function postLoad(LifecycleEventArgs $args)
  {
    $object = $args->getObject();

    if(!$object instanceof Order)
    {
      return;
    }

    $shippingMethod = $this->shippingManager->getShippingMethodByUid($object->getShippingMethodId());

    $object->setShippingMethod($shippingMethod);
  }
}