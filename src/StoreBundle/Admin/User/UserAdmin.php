<?php

namespace StoreBundle\Admin\User;

use Sonata\AdminBundle\Show\ShowMapper;
use StoreBundle\Entity\User\User;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class UserAdmin extends AbstractAdmin
{
  protected $translationDomain = 'messages';

  protected function configureListFields(ListMapper $list)
  {
    $list
      ->add('email')
      ->add('firstname')
      ->add('lastname')
      ->add('phone')
      ->add('enabled', null, [
        'editable' => true
      ])
      ->add('_action', null, [
        'actions' => [
          'edit' => [],
          'delete' => [],
          'show' => [],
        ]
      ]);

  }

  protected function configureFormFields(FormMapper $form)
  {
    /** @var User $user */
    $user = $this->getSubject();
    $is_edit = $user->getId() !== null;

    $form
      ->tab('Основное')
      ->with('Данные пользователя')
      ->add('email', null, [
      #  'disabled' => !$is_edit
      ])
      ->add('firstname')
      ->add('middlename')
      ->add('lastname')
      ->add('plainPassword', PasswordType::class, [
        'label' => !$is_edit ? 'Пароль' : 'Новый пароль',
        'help' => 'Не менее 5 символов',
        'attr' => [
          'placeholder' => '*****'
        ],
        'required' => !$is_edit
      ])
      ->add('phone')
      ->add('enabled')
      ->end()
      ->with('Роли')
      ->add('admin', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', [
        'label' => 'Администратор',
      ])
      ->add('operator', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', [
        'label' => 'Оператор',
      ])
      ->end()
      /*->add('city', null, [
        'required' => false,
      ])*/
     /* ->add('contragentStatus', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', [
        'choices' => User::getAvailableContragentStatuses()
      ])*/
      ->end();
/*      ->tab('Компания')
        ->add('company', 'StoreBundle\Form\User\CompanyFormType', [
          'required' => false,
        ])
      ->end();*/

    #$builder = $form->getFormBuilder();
    #$builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'preSubmit']);
    #$builder->addEventListener(FormEvents::POST_SUBMIT, [$this, 'postSubmit']);
  }

  protected function configureDatagridFilters(DatagridMapper $filter)
  {
    $filter
      ->add('firstname')
      ->add('lastname')
      ->add('phone')
      ->add('enabled')
      ->add('roles', null, [], 'choice', [
        'choices' => User::getAvailableRoles()
      ]);
  }

  protected function configureShowFields (ShowMapper $show)
  {
    $show
      ->with('Основное')
        ->add('fio')
        ->add('phone')
        ->add('email')
      #  ->add('city')
        ->add('roles', null, [
          'label' => 'Роль',
          'template' => '@Store/Admin/User/main_role_show.html.twig',
        ])
      ->end()
 /*     ->with('Документы')
        ->add('documents', null, [
          'template' => '@Store/Admin/User/documents_show_array.html.twig',
        ])
      ->end()*/;
  }

  public function preSubmit(FormEvent $event)
  {
    $form = $event->getForm();
    $data = $event->getData();
    $status = $data['contragentStatus'];

    if ($status !== User::ROLE_JURIDICAL)
    {
      /*
       * Не заставляем создавать компанию, если она не нужна
       */
      if (!$this->getSubject()->getCompany())
      {
        $form->remove('company');
        unset($data['company']);
        $event->setData($data);
      }
    }
  }
  
  /**
   * @param $object User
   */
  public function preUpdate($object)
  {
    parent::preUpdate($object);
    
    if($object->getPlainPassword())
    {
      $userManager = $this->getConfigurationPool()->getContainer()->get('fos_user.user_manager');
      $userManager->updateUser($object, false);
    }
  }
}