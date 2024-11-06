<?php

namespace Accurateweb\LogisticBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class CityAdmin extends AbstractAdmin
{
  protected function configureFormFields (FormMapper $form)
  {
    $form->add('name')
         ->add('pickupPoint', 'sonata_type_admin', [
           'by_reference' => false
         ])
    ;
  }

  protected function configureListFields (ListMapper $list)
  {
    $list
      ->add('name')
      ->add('_action', null, array(
        'actions' => array(
          'edit' => array(),
          'delete' => array()
        )
      ));
  }

  protected function configureDatagridFilters (DatagridMapper $filter)
  {
  }
}