<?php

namespace StoreBundle\Form\Text;


use StoreBundle\Entity\Text\Question;
use Symfony\Component\DomCrawler\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserQuestionType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('fio')
      ->add('phone', null, [
        'required' => false
      ])
      ->add('email', null, [
        'required' => false
      ])
      ->add('text')
      ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event)
      {
        /** @var Question $question */
        $question = $event->getData();
        
        if(!$question->getEmail() && !$question->getPhone())
        {
          $phoneAndEmailNullFormError = new FormError('Введите телефон или почту, чтобы оператор мог связаться с Вами!');
          $event->getForm()->get('email')->addError($phoneAndEmailNullFormError);
          $event->getForm()->get('phone')->addError($phoneAndEmailNullFormError);
        }
        
        if($question->getEmail())
        {
         if( filter_var($question->getEmail(), FILTER_VALIDATE_EMAIL) === false)
         {
           $incorrectEmailFormError = new FormError('Email невалиден, введите email вида example@mail.ru');
           $event->getForm()->get('email')->addError($incorrectEmailFormError);
         }
        }
      });
  }
  
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => Question::class,
      'csrf_protection' => false,
    ));
  }
}