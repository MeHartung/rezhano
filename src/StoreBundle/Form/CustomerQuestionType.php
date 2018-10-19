<?php

namespace StoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class CustomerQuestionType extends AbstractType
{
  public function buildForm (FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('userName', 'Symfony\Component\Form\Extension\Core\Type\TextType',[
        'required' => true,
        'label' => 'Фамилия и имя',
        'constraints' => [
          new NotBlank(),
        ]
      ])
      ->add('userEmail', 'Symfony\Component\Form\Extension\Core\Type\TextType',[
        'required' => true,
        'label' => 'Электронная почта',
        'constraints' => [
          new NotBlank(),
          new Email(),
        ]
      ])
      ->add('message', 'Symfony\Component\Form\Extension\Core\Type\TextareaType',[
        'required' => true,
        'label' => 'Ваш вопрос',
        'constraints' => [
          new NotBlank(),
        ]
      ]);
  }

  public function configureOptions (OptionsResolver $resolver)
  {
    $resolver->setDefault('data_class', 'StoreBundle\Entity\Text\Dialog\DialogMessage');
  }
}