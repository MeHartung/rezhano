<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 09.08.17
 * Time: 13:23
 */

namespace StoreBundle\Admin\Products\Type;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class ProductTypeAdmin extends AbstractAdmin
{

  public function configureListFields(ListMapper $list)
  {
   $list
     ->add('name')
     ->add('productAttributes', 'sonata_type_model_autocomplete', array(
       'multiple' => 'true',
       'editable' => 'true',
       'label' => 'Свойства'
     ))
     ->add('_action', null, [
       'actions' => [
         'edit' => [],
         'delete' => []
       ]
     ])
     ;
  }

  public function configureFormFields(FormMapper $form)
  {
    $form
      ->tab('Тип')
        ->add('name')
      ->end()
      ->end()

      ->tab('Свойства')
        ->add('productAttributes', 'sonata_type_model', array('multiple' => true))
      ->end()
      ->end()
    ;
  }



}