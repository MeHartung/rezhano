<?php

namespace StoreBundle\DataAdapter\Notification;


use Accurateweb\ClientApplicationBundle\DataAdapter\ClientApplicationModelAdapterInterface;
use Accurateweb\SettingBundle\Model\Manager\SettingManagerInterface;
use StoreBundle\Entity\Text\Dialog\DialogMessage;

class DialogMessageDataAdapter implements ClientApplicationModelAdapterInterface
{
  private $settingManager;

  public function __construct (SettingManagerInterface $settingManager)
  {
    $this->settingManager = $settingManager;
  }

  /**
   * @param DialogMessage $subject
   * @param array $options
   * @return array
   */
  public function transform ($subject, $options = array())
  {
    $dialog = $subject->getDialog();
    $owner = false;
    $userName = $subject->getUserName();

    if ($dialog && $dialog->getCreator() && $subject->getUser())
    {
      $owner = $dialog->getCreator()->getId() === $subject->getUser()->getId();
    }

    if (!$owner)
    {
      /*
       * Ставим имя пользователя "Поддержка" для сообщений, которые были созданы не пользователем
       */
      $userName = $this->settingManager->getValue('operator_name');
    }

    return [
      'message' => $subject->getMessage(),
      'userName' => $userName,
      'userEmail' => $subject->getUserEmail(),
      'date' => $subject->getCreatedAt()->format('d.m.Y H:i'),
      'id' => $subject->getId(),
      'isOwner' => $owner, //Сообщение написано создателем диалога
    ];
  }

  public function getModelName ()
  {
    return 'Message';
  }

  public function supports ($subject)
  {
    return $subject instanceof DialogMessage;
  }

}