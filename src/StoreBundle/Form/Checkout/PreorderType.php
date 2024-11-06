<?php

namespace StoreBundle\Form\Checkout;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class PreorderType extends AbstractType
{
  /**
   * {@inheritdoc}
   */
  public function buildForm (FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('firstname', TextType::class, [
        'required' => true,
        'constraints' => [
          new NotBlank()
        ]
      ])
      ->add('email', EmailType::class, [
        'required' => true,
        'constraints' => [
          new Email()
        ]
      ])
      ->add('phone', TextType::class, [
        'required' => true,
        'constraints' => [
          new NotBlank(),
          new Regex(['pattern' => '/\+7\s\(\d{3}\)\s\d{3}\-\d{2}\-\d{2}/'])
        ]
      ])
      ->add('product_slug', HiddenType::class, [
        'required' => false,
        'mapped' => false
      ]);
  }

}