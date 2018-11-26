<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 09.08.17
 * Time: 13:08
 */

namespace StoreBundle\Admin\Products;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\Filter\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class ProductAttributeAdmin extends AbstractAdmin
{
  protected $datagridValues = array(
    '_page' => 1,
    '_sort_order' => 'ASC',
    '_sort_by' => 'weight',
  );
  
  protected $translationDomain = 'messages';

  public function configureListFields(ListMapper $list)
  {
    $list
      ->add('name')
      ->add('units')
      ->add('type', 'choice', [
        'editable' => true,
        'choices' => [
          1 => 'Числовой',
          2 => 'Cтроковый'
        ],
      ])
      ->add('valueType', 'choice', [
        'editable' => true,
        'choices' => [
          1 => 'Статичное',
          2 => 'Вариативное'
        ]
      ])
      ->add('productAttributeValues', 'sonata_type_model_autocomplete', array(
        'property' => 'value',
        'label' => 'Значения свойства товара'
      ))
      ->add('weight', null, array(
        'label' => 'Вес',
        'editable' => true
      ))
      ->add('_action', null, [
        'actions' => [
          'edit' => [],
          'delete' => []
        ]
      ]);
  }

  public function configureFormFields(FormMapper $form)
  {
    $form
      ->tab('Свойство товара')
      ->add('name', 'text')
      ->add('units', 'text')
      ->add('type', ChoiceType::class, [
        'choices' => [
          'Числовой' => 1,
          'Cтроковый' => 2
        ],
      ])
      ->add('value_type', ChoiceType::class, [
        'choices' => [
          'Статичное' => 1,
          'Вариативное' => 2
        ],
      ])
      ->add('weight', \Symfony\Component\Form\Extension\Core\Type\NumberType::class, array(
        'required' => true,
        'constraints' => array(
          new NotBlank(),
          new Range(['min'=> 0, 'minMessage' => 'Введите число не менее 0'])
        ),
        'label' => 'Вес'
      ))
      ->add('showInFilter', null, [
        'label' => 'Выводить аттрибут в фильтре'
      ])
      ->add('showInProduct', null, [
        'label' => 'Выводить аттрибут в товаре'
      ])
      ->end()
      ->end()
      ->tab('Значения свойства товара')
      ->add('productAttributeValues', 'sonata_type_collection', array(
        'label' => false
      ), array(
        'edit' => 'inline',
        'inline' => 'table',
      ))
      ->end()
      ->end();

  }

  public function preUpdate($object)
  {
    foreach ($object->getProductAttributeValues() as $value)
    {
      $value->setProductAttribute($object);
    }
  }

  public function prePersist($object)
  {
    foreach ($object->getProductAttributeValues() as $value)
    {
      $value->setProductAttribute($object);
    }
  }
}