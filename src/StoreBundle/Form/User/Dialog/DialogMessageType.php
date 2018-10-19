<?php

namespace StoreBundle\Form\User\Dialog;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class DialogMessageType extends AbstractType
{
  public function buildForm (FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('message', null, [
        'constraints' => [
          new NotBlank()
        ],
        'required' => true,
      ]);
  }

  public function configureOptions (OptionsResolver $resolver)
  {
    $resolver
      ->setDefault('data_class', 'StoreBundle\Entity\Text\Dialog\DialogMessage');
  }
}