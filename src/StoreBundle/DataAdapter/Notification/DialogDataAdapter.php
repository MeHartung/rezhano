<?php

namespace StoreBundle\DataAdapter\Notification;


use Accurateweb\ClientApplicationBundle\DataAdapter\ClientApplicationModelAdapterInterface;
use StoreBundle\Entity\Text\Dialog\Dialog;

class DialogDataAdapter implements ClientApplicationModelAdapterInterface
{
  private $messageDataAdapter;

  public function __construct (DialogMessageDataAdapter $messageDataAdapter)
  {
    $this->messageDataAdapter = $messageDataAdapter;
  }

  /**
   * @param Dialog $subject
   * @param array $options
   * @return array
   */
  public function transform ($subject, $options = array())
  {
    $messages = [];

    foreach ($subject->getMessages() as $message)
    {
      $messages[] = $this->messageDataAdapter->transform($message);
    }

    return [
      'id' => $subject->getId(),
      'creatorName' => $subject->getCreator()?$subject->getCreator()->getFio():'',
      'last_message_at' => $subject->getUpdatedAt()?$subject->getUpdatedAt()->format('Y-m-d H:i'):null,
      'created_at' => $subject->getCreatedAt()->format('Y-m-d H:i'),
      'type' => $subject->getDialogType(),
      'messages' => $messages,
    ];
  }

  public function getModelName ()
  {
    return 'Dialog';
  }

  public function supports ($subject)
  {
    return $subject instanceof Dialog;
  }

}