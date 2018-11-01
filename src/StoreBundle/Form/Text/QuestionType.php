<?php

namespace StoreBundle\Form\Text;

use Sonata\CoreBundle\Form\Type\BooleanType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
          ->add('fio', null, [
            'disabled' => true
          ])
          ->add('email', null, [
            'disabled' => true
          ])
          ->add('phone', null, [
            'disabled' => true
          ])
          ->add('text', null, [
            'disabled' => true
          ])
          ->add('answer')/*
          ->add('answerAt', HiddenType::class, [
            'required' => false,
          ])*/;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'StoreBundle\Entity\Text\Question'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'storebundle_text_question';
    }


}
