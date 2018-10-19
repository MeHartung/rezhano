<?php

namespace Accurateweb\LogisticBundle\Admin;


use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Validator\Constraints\Regex;

class WarehouseAdmin extends AbstractAdmin
{
  protected function configureFormFields (FormMapper $form)
  {
    $form
      ->add('name')
      ->add('address')
      ->add('city')
      ->add('latitude', null, [
        'constraints' => [
          new Regex([
            'pattern' => '/^[\d]{1,9}(\.[\d]{1,6})?$/',
            'message' => 'Необходимо ввести корректные координаты',
          ])
        ]
      ])
      ->add('longitude', null, [
        'constraints' => [
          new Regex([
            'pattern' => '/^[\d]{1,9}(\.[\d]{1,6})?$/',
            'message' => 'Необходимо ввести корректные координаты',
          ])
        ]
      ]);
  }

  protected function configureListFields (ListMapper $list)
  {
    $list
      ->add('name')
      ->add('address')
      ->add('city')
      ->add('_action', null, array(
        'actions' => array(
          'edit' => array(),
          'delete' => array()
        )
      ));
  }

  protected function configureDatagridFilters (DatagridMapper $filter)
  {
    $filter
      ->add('city');
  }
}