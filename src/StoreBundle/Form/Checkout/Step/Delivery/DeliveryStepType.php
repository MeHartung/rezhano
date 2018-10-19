<?php

namespace StoreBundle\Form\Checkout\Step\Delivery;

use AccurateCommerce\Shipping\Method\Excam\ShippingMethodExcamPickup;
use AccurateCommerce\Shipping\Method\ShippingMethod;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Range;

abstract class DeliveryStepType extends AbstractType
{
  public function buildForm (FormBuilderInterface $builder, array $options)
  {
    $shippingMethods = $options['shippingMethods'];
    $shippingChoices = [];

    /** @var ShippingMethod $shippingMethod */
    foreach ($shippingMethods as $shippingMethod)
    {
      $shippingChoices[$shippingMethod->getName()] = $shippingMethod->getUid();
    }

    $builder
      ->add('shippingMethodId', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', [
        'choices' => $shippingChoices,
        'expanded' => true,
        'empty_data' => ShippingMethodExcamPickup::UID,
        'invalid_message' => 'К сожалению, сейчас вы не можете выбрать этот способ доставки',
        'constraints' => [
          new NotNull(),
        ]
      ])
//      ->add('shippingAddress', null, [
//        'required' => false,
//        'constraints' => [
//        ]
//      ])
      ->add('shippingDate', 'Symfony\Component\Form\Extension\Core\Type\DateType', [
        'required' => false,
        'widget' => 'single_text',
        'format' => 'dd.MM.yyyy',
        'html5' => false,
        'constraints' => [
          new Range([
            'min' => 'today',
            'minMessage' => 'Вы не можете забрать заказ раньше, чем сегодня',
            'max' => '+1 month',
            'maxMessage' => 'Вы должны забрать товар позже, чем через месяц',
          ]),
        ]
      ])
      ->add('customer_comment', 'Symfony\Component\Form\Extension\Core\Type\TextareaType', [
        'required' => false,
        'label' => 'Сопроводительные документы',
      ]);
  }

  public function configureOptions (OptionsResolver $resolver)
  {
    $resolver->setDefault('data_class', 'StoreBundle\Entity\Store\Order\Order');
    $resolver->setRequired('shippingMethods');
  }
}