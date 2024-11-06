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
use Sonata\CoreBundle\Form\Type\BooleanType;
use StoreBundle\Entity\Store\Catalog\Product\Attributes\Type\ProductType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvents;

class ProductTypeAdmin extends AbstractAdmin
{

  public function configureListFields(ListMapper $list)
  {
   $list
     ->add('name')
     ->add('measured', BooleanType::class, array(
       'transform' => true
     ))
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
        ->add('name', null, [
          'required' => true
        ])
        ->add('measured', BooleanType::class, array(
          'transform' => true
        ))
        ->add('minCount', NumberType::class, [
          'required' => true,
        ])
        ->add('countStep', NumberType::class, [
          'required' => true,
        ])
      ->end()
      ->end()

      ->tab('Свойства')
        ->add('productAttributes', 'sonata_type_model', array('multiple' => true))
      ->end()
      ->end()
    ;
    
    /** @var ProductType $subject */
    $subject = $this->getSubject();
    $form->getFormBuilder()->addEventListener(FormEvents::POST_SUBMIT,
      function (\Symfony\Component\Form\FormEvent $event) use ($subject)
      {
        $form = $event->getForm();
      
        if($subject->getCountStep() < 0)
        {
          $form->get('countStep')->addError(new FormError('Шаг не может быть меньше 0'));
        }
      
        if($subject->getMinCount() < 0)
        {
          $form->get('countStep')->addError(new FormError('Минимальное количество для заказа  не может быть меньше 0'));
        }
      });
  }



}