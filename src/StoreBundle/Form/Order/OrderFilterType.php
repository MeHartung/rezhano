<?php

namespace StoreBundle\Form\Order;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class OrderFilterType extends AbstractType
{
  public function buildForm (FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('date', 'StoreBundle\Form\Common\DateIntervalType', [
        'required' => false,
      ])
      ->add('city', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', [
        'class' => 'StoreBundle\Entity\Store\Logistics\Delivery\Cdek\CdekCity',
        'required' => false,
      ])
      ->add('mtr', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', [
        'choices' => [
          'MTR' => true,
          'no-MTR' => false,
        ],
        'required' => false,
        'placeholder' => false,
      ]);
  }
}