<?php

namespace StoreBundle\DataAdapter\Order;

use Accurateweb\ClientApplicationBundle\DataAdapter\ClientApplicationModelAdapterInterface;
use StoreBundle\Entity\Store\Order\Status\OrderStatusHistory;

class OrderStatusHistoryDataAdapter implements ClientApplicationModelAdapterInterface
{
  private $orderStatusDataAdapter;

  public function __construct (OrderStatusDataAdapter $orderStatusDataAdapter)
  {
    $this->orderStatusDataAdapter = $orderStatusDataAdapter;
  }

  /**
   * @param OrderStatusHistory $subject
   * @param array $options
   * @return array
   */
  public function transform ($subject, $options = array())
  {
    return [
      'id' => $subject->getId(),
      'status' => $this->orderStatusDataAdapter->transform($subject->getStatus()),
      'reason' => $subject->getReason(),
      'created_at' => $subject->getCreatedAt()->format('d.m.Y H:i')
    ];
  }

  public function getModelName ()
  {
    return 'OrderStatusHistory';
  }

  public function supports ($subject)
  {
    return $subject instanceof OrderStatusHistory;
  }
}