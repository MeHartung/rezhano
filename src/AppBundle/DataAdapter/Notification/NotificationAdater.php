<?php

namespace AppBundle\DataAdapter\Notification;

use Accurateweb\ClientApplicationBundle\DataAdapter\ClientApplicationModelAdapterInterface;
use StoreBundle\Entity\Notification\DialogNotification;
use StoreBundle\Entity\Notification\Notification;
use StoreBundle\Entity\Notification\OrderNotification;
use StoreBundle\Entity\Notification\TextNotification;

class NotificationAdater implements ClientApplicationModelAdapterInterface
{
  private $dialogAdapter;

  public function __construct (DialogDataAdapter $dialogAdapter)
  {
    $this->dialogAdapter = $dialogAdapter;
  }

  /**
   * @param Notification $subject
   * @param array $options
   * @return array
   */
  public function transform ($subject, $options = array())
  {
    $data = [
      'id' => $subject->getId(),
      'create_at' => $subject->getCreatedAt()->format('d.m.Y H:i'),
      'read' => $subject->isRead(),
      'message' => $subject->getMessage(),
      'author' => $subject->getAuthor(),
      'type' => $subject->getType(),
      'title' => $subject->getTitle(),
      'orderNumber' => '',
    ];

    switch (true)
    {
      case $subject instanceof DialogNotification:
        $data['dialog'] = $this->dialogAdapter->transform($subject->getDialog());
        $data['create_at'] = $subject->getDialog()->getUpdatedAt()->format('d.m.Y H:i');
        break;
      case $subject instanceof TextNotification:
        break;
      case $subject instanceof OrderNotification:
        $data['orderNumber'] = $subject->getOrder()->getDocumentNumber();
        break;
    }

    return $data;
  }

  public function getModelName ()
  {
    return 'Notification';
  }

  public function supports ($subject)
  {
    return $subject instanceof Notification;
  }

}