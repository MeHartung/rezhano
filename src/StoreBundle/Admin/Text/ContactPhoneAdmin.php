<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Admin\Text;


use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use StoreBundle\Form\PhoneType;
use Symfony\Component\Validator\Constraints\Regex;

class ContactPhoneAdmin extends AbstractAdmin
{
  protected function configureFormFields(FormMapper $form)
  {
    $form
      ->add('name')
      ->add('phone', PhoneType::class, [
        'label' => 'Телефон',
        'attr' => [
          'placeholder' => '+7 (___) ___ - __ - __',
        ],
        'constraints' => array(
          new Regex(array(
            'pattern' => '/^\+7\s?\(\d{3}\)\s?\d{3}(\-\d{2}){2}$/',
            'message' => 'Некорректный номер телефона'
          ))
        )
      ])
      ->add('published')
    ;
  }

  protected function configureListFields(ListMapper $list)
  {
    $list
      ->add('name')
      ->add('phone')
      ->add('published', null, [
        'editable' => true,
      ])
      ->add('_action', null, array(
        'actions' => [
          'edit' => [],
          'delete' => []
        ]
      ));
    ;
  }
}