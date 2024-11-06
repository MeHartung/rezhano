<?php

namespace StoreBundle\Admin\Text;

use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use StoreBundle\Entity\Notification\DialogNotification;
use StoreBundle\Entity\Text\Dialog\Dialog;
use StoreBundle\Event\CustomerQuestionEvent;
use StoreBundle\EventListener\Notification\DialogNotificationListener;

class CustomerQuestionAdmin extends AbstractAdmin
{
  protected $translationDomain = 'messages';
  protected $datagridValues = array(
    '_page' => 1,
    '_sort_order' => 'DESC',
    '_sort_by' => 'createdAt',

  );

  protected function configureListFields (ListMapper $list)
  {
    $list
      ->add('creator.fio')
      ->add('createdAt', null, [
        'format' => 'd.m.Y',
      ])
      ->add('updatedAt', null, [
        'format' => 'd.m.Y H:i',
        'label' => 'Последнее сообщение',
      ])
      ->add('lastMessage', 'html', [
        'label' => 'Последнее сообщение',
        'truncate' => [
          'length' => 50
        ],
      ])
      ->add('_action', null, array(
        'actions' => [
          'show' => [],
          'edit' => [],
        ]
      ));
  }

  protected function configureFormFields (FormMapper $form)
  {
    $form->add('messages', 'Sonata\CoreBundle\Form\Type\CollectionType', [
      'btn_add' => 'Добавить ответ',
      'by_reference' => false,
    ], [
      'edit' => 'inline',
      'inline' => 'table',
      'sortable' => 'parent',
    ]);
  }

  protected function configureShowFields (ShowMapper $show)
  {
    $show
      ->add('createdAt')
      ->add('updatedAt')
      ->add('messages', null, [
        'template' => '@Store/Admin/Text/CustomerQuestion/show_dialog.html.twig'
      ]);
  }

  public function createQuery ($context = 'list')
  {
    /** @var QueryBuilder $query */
    $query = parent::createQuery($context);
    $alias = $query->getRootAliases()[0];

    $query->andWhere($alias.'.dialogType = :dialogType');
    $query->setParameter('dialogType', Dialog::DIALOG_TYPE_QUESTION);

    return $query;
  }

  /**
   * @param Dialog $object
   */
  public function preUpdate ($object)
  {
    $this->insertCurrentUser($object);
    $this->setDialogNotificationUnread($object);

    foreach ($object->getMessages() as $message)
    {
      if (!$message->getId())
      {
        $this->getConfigurationPool()->getContainer()->get('event_dispatcher')->dispatch('customer_question.answer', new CustomerQuestionEvent($message));
      }
    }

    parent::preUpdate($object);
  }

  /**
   * @param Dialog $object
   */
  public function prePersist ($object)
  {
    $this->insertCurrentUser($object);

    parent::prePersist($object);
  }

  /**
   * @param Dialog $object
   * Для сообщений, создаваемых в админке будет проставлять только имя пользователя
   * имя берется из настроек
   */
  private function insertCurrentUser($object)
  {
//    $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();

    foreach ($object->getMessages() as $message)
    {
      if (!$message->getId())
      {
        $adminName = $this->getConfigurationPool()->getContainer()->get('aw.settings.manager')->getValue('operator_name');
//        $message->setUser($user);
//        $message->setUserName($user->getFio());
        $message->setUserName($adminName);
//        $message->setUserEmail($user->getEmail());
      }
    }
  }

  /**
   * @param Dialog $dialog
   */
  protected function setDialogNotificationUnread($dialog)
  {
    $notification = $dialog->getNotification();

    if (!$notification)
    {
      /**
       * По сути если попали сюда, то нарушилась логика работы приложения,
       *   т.к не сработал
       * @see DialogNotificationListener
       */
      $notification = new DialogNotification();
      $dialog->setNotification($notification);
      $notification->setUser($dialog->getCreator());
      $notification->setCreatedAt(new \DateTime());
    }

    $notification->setRead(false);
    $notification->setReadAt(null);

    $em = $this->getConfigurationPool()->getContainer()->get('doctrine.orm.entity_manager');
    $em->persist($notification);
    $em->flush($notification);
  }
}