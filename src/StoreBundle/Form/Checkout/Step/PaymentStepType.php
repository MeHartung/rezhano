<?php

namespace StoreBundle\Form\Checkout\Step;

use AccurateCommerce\Component\Payment\Model\PaymentMethodInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class PaymentStepType extends AbstractType
{
  public function buildForm (FormBuilderInterface $builder, array $options)
  {
    $paymentMethods = $options['paymentMethods'];
    $paymentChoices = [];
    $descriptions = [];

    /** @var PaymentMethodInterface $paymentMethod */
    foreach ($paymentMethods as $paymentMethod)
    {
      $paymentChoices[$paymentMethod->getName()] = $paymentMethod;
      $descriptions[$paymentMethod->getId()] = $paymentMethod->getDescription();
    }

    $builder
      ->add('customerPhone')
      ->add('customerFirstName')
      ->add('paymentMethod', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', [
        'class' => 'StoreBundle\Entity\Store\Payment\Method\PaymentMethod',
        'required' => true,
        'error_bubbling' => false,
        'constraints' => [
          new NotBlank(['message' => 'Пожалуйста, укажите способ оплаты заказа'])
        ],
        'choices' => $paymentChoices,
        'choice_attr' => function($choiceValue, $key, $value) use($descriptions){
          //TODO колонку сделать с иконками
          $icon = null;

          if ($key === 'Онлайн-оплата')
          {
            $icon = 'payment-online-icon';
          }
          elseif($key === 'Безналичный расчет')
          {
            $icon = 'payment-pickup-icon';
          }

          return [
            'data-description' => $descriptions[$value],
            'data-icon' => $icon
          ];
        }
      ]);
  }

  public function configureOptions (OptionsResolver $resolver)
  {
    $resolver->setDefault('data_class', 'StoreBundle\Entity\Store\Order\Order');
    $resolver->setRequired('paymentMethods');
  }


}