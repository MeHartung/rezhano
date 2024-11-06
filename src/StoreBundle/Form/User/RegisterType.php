<?php

namespace StoreBundle\Form\User;

use StoreBundle\DataTransformer\UserCompanyDataTransformer;
use StoreBundle\Entity\User\User;
use FOS\UserBundle\Form\Type\RegistrationFormType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotEqualTo;
use Symfony\Component\Validator\Constraints\Regex;

class RegisterType extends RegistrationFormType
{
  public function __construct ()
  {
    $class = 'StoreBundle\Entity\User\User';
    parent::__construct($class);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm (FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('roles', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', [
        'choices' => [
          'Юридическое лицо' => User::ROLE_JURIDICAL,
          'Физическое лицо' => User::ROLE_INDIVIDUAL,
          'Индивидуальный предприниматель' => User::ROLE_ENTREPRENEUR,
        ],
        'data' => User::ROLE_JURIDICAL,
        'expanded' => true,
      ])
      ->add('firstname', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
        'label' => 'Фамилия'
      ])
      ->add('lastname', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
        'label' => 'Имя'
      ])
      ->add('middlename', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
        'label' => 'Отчество',
        'required' => false,
      ])
      ->add('phone', 'StoreBundle\Form\PhoneType', [
        'label' => 'Номер телефона',
      ])
      ->add('email', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
        'label' => 'form.email',
        'translation_domain' => 'FOSUserBundle',
        'constraints' => [
          new NotBlank(),
          new Email()
        ]
      ))
      ->add('city', null, array(
        'label' => 'Город',
      ))
      ->add('plainPassword', 'Symfony\Component\Form\Extension\Core\Type\RepeatedType', array(
        'type' => 'Symfony\Component\Form\Extension\Core\Type\PasswordType',
        'options' => array('translation_domain' => 'FOSUserBundle'),
        'first_options' => array('label' => 'form.password'),
        'second_options' => array('label' => 'form.password_confirmation'),
        'invalid_message' => 'fos_user.password.mismatch'
      ))
      ->add('tos', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', [
        'required' => true,
        'mapped' => false,
        'label' => 'Я ознакомился и согласен с Политикой компании и условиями сотрудничества',
        'constraints' => [
          new NotEqualTo(false)
        ]
      ]);

    $builder->get('roles')->resetModelTransformers()
      ->addModelTransformer(new CallbackTransformer([$this, 'transformRoles'], [$this, 'reverseTransformRoles']));
  }

  public function transformRoles($roles)
  {
    if (is_array($roles) && count($roles))
    {
      return $roles[0];
    }

    return $roles;
  }

  public function reverseTransformRoles($roles)
  {
    if (is_string($roles))
    {
      return [$roles];
    }

    return $roles;
  }
}