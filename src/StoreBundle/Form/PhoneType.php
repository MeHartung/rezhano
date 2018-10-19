<?php

namespace StoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class PhoneType extends AbstractType
{
  public function getName ()
  {
    return 'phone';
  }

  public function getParent ()
  {
    return 'Symfony\Component\Form\Extension\Core\Type\TextType';
  }

  public function setDefaultOptions (OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'label' => 'Телефон',
      'attr' => [
        'placeholder' => '+7 (___) ___ - __ - __',
      ],
      'constraints' => array(
        new Regex(array(
          'pattern' => '/^\+7\(9\d{2}\)\d{3}(\-\d{2}){2}$/',
          'message' => 'Некорректный номер телефона'
        ))
      )
    ));
  }
}
