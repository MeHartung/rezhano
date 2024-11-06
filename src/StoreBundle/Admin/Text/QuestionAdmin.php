<?php

namespace StoreBundle\Admin\Text;


use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use StoreBundle\Entity\Text\Question;
use StoreBundle\Event\QuestionAnswerEvent;

class QuestionAdmin extends AbstractAdmin
{
  public function configureListFields(ListMapper $list)
  {
    $list
      ->add('fio')
      ->add('email')
      ->add('phone')
      ->add('source', null, [
        'label' => 'Источник записи'
      ])
      ->add('createdAt', 'datetime', [
        'format' => 'd.m.Y H:i',
      ])
      ->add('answerAt', 'datetime', [
        'format' => 'd.m.Y H:i',
      ])
      ->add('_action', null, array(
          'actions' => array(
            'edit' => null,
            'delete' => null
          )
        )
      );
  }
  
  public function configureFormFields(FormMapper $form)
  {
    $form
      ->add('fio')
      ->add('email')
      ->add('phone')
      ->add('text')
      ->add('answer')
    ;
  }

}