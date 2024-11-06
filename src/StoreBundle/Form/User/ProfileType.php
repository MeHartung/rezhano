<?php

namespace StoreBundle\Form\User;


use StoreBundle\Entity\User\User;
use FOS\UserBundle\Form\Type\ProfileFormType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;

class ProfileType extends ProfileFormType
{
  public function __construct ()
  {
    $class = User::class;
    parent::__construct($class);
  }
  /**
   * {@inheritdoc}
   */
  protected function buildUserForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('email', EmailType::class, [
        'label' => 'Электронная почта',
      ])
      ->add('lastname', TextType::class, [
        'required' => false,
        'label' => 'Фамилия'
      ])
      ->add('firstname', TextType::class, [
        'required' => false,
        'label' => 'Имя'
      ])
      ->add('middlename', TextType::class, [
        'required' => false,
        'label' => 'Отчество'
      ])
      ->add('phone', TextType::class, [
        'required' => true,
        'label' => 'Номер телефона',
        'invalid_message' => 'Введите правильный номер телефона',
        'constraints' => [
          new Regex(['pattern' => '/\+7 \(\d{3}\) \d{3}\-\d{2}\-\d{2}/'])
        ]
      ])
      ->add('city');
  }

  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    parent::buildForm($builder, $options);
    $builder->remove('current_password');
//    $builder->add('plainPassword', 'repeated', array(
//      'type' => 'password',
//      'options' => array('translation_domain' => 'FOSUserBundle'),
//      'first_options' => array('label' => 'form.new_password'),
//      'second_options' => array('label' => 'form.new_password_confirmation'),
//      'invalid_message' => 'fos_user.password.mismatch',
//    ));
  }
}