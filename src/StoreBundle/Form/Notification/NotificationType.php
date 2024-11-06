<?php

namespace StoreBundle\Form\Notification;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NotificationType extends AbstractType
{
  public function buildForm (FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('read', null, [
        'required' => false,
      ]);
  }

  public function configureOptions (OptionsResolver $resolver)
  {
    $resolver
      ->setDefault('data_class', 'StoreBundle\Entity\Notification\Notification');
  }
}