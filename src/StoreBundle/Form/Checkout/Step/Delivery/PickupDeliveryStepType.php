<?php

namespace StoreBundle\Form\Checkout\Step\Delivery;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PickupDeliveryStepType extends DeliveryStepType
{
  public function buildForm (FormBuilderInterface $builder, array $options)
  {
    parent::buildForm($builder, $options);
//    $builder->get('shippingMethodId')->setData($options['shippingMethodId']);
    $builder->remove('shippingMethodId');
    $builder->add('shippingMethodId', 'Symfony\Component\Form\Extension\Core\Type\HiddenType', [
      'data' => $options['shippingMethodId']
    ]);
  }

  public function configureOptions (OptionsResolver $resolver)
  {
    parent::configureOptions($resolver);
    $resolver->setRequired('shippingMethodId');
  }

  public function getBlockPrefix ()
  {
    return 'typePickup';
  }
}