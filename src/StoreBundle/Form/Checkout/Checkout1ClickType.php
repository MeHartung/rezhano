<?php

namespace StoreBundle\Form\Checkout;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */
class Checkout1ClickType extends AbstractType
{
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder->add('phone', TextType::class, [
      'required' => true,
      'constraints' => [
        new NotBlank(),
        new Regex(['pattern' => '/\+7\s\(\d{3}\)\s\d{3}\-\d{2}\-\d{2}/'])
      ]
    ]);
  }

}