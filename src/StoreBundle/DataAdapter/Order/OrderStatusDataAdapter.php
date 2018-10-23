<?php

namespace AppBundle\DataAdapter\Order;

use Accurateweb\ClientApplicationBundle\DataAdapter\ClientApplicationModelAdapterInterface;
use StoreBundle\Entity\Store\Order\Status\OrderStatus;

class OrderStatusDataAdapter implements ClientApplicationModelAdapterInterface
{
  /**
   * @param OrderStatus $subject
   * @param array $options
   * @return array
   */
  public function transform ($subject, $options = array())
  {
    return [
      'id' => $subject->getId(),
      'name' => $subject->getName(),
      'type' => $subject->getType()?$subject->getType()->getName():null,
    ];
  }

  public function getModelName ()
  {
    return 'OrderStatus';
  }

  public function supports ($subject)
  {
    return $subject instanceof OrderStatus;
  }
}