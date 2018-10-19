<?php

namespace StoreBundle\Form\User;

use StoreBundle\Validator\Constraints\Inn;
use StoreBundle\Validator\Constraints\Kpp;
use StoreBundle\Validator\Constraints\Ogrn;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class CompanyFormType extends AbstractType
{
  public function buildForm (FormBuilderInterface $builder, array $options)
  {
    $builder
//      ->add('organisation', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
//        'mapped' => false,
//      ])
      ->add('name', null, [
        'constraints' => [
          new NotBlank(),
        ]
      ])
      ->add('inn', null, [
        'constraints' => [
          new Inn(),
        ]
      ])
      ->add('kpp', null, [
        'constraints' => [
          new Kpp(),
        ]
      ])
      ->add('ogrn', null, [
        'constraints' => [
          new Ogrn(),
        ]
      ])
      ->add('country', null, [
        'constraints' => [
          new NotBlank(),
        ]
      ])
      ->add('address', null, [
        'constraints' => [
          new NotBlank(),
        ]
      ])
      ->add('director', null, [
        'constraints' => [
          new NotBlank(),
        ]
      ])
      ->add('phone', 'StoreBundle\Form\PhoneType', [
        'required' => true,
        'constraints' => [
          new NotBlank(),
        ]
      ])
      ->add('email', null, [
        'constraints' => [
          new NotBlank(),
          new Email(),
        ]
      ]);
  }

  public function configureOptions (OptionsResolver $resolver)
  {
    $resolver->setDefault('data_class', 'StoreBundle\Entity\User\Company');
  }


}