<?php

namespace StoreBundle\Admin\Store\Brand;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class BrandAdmin extends AbstractAdmin
{
  protected function configureFormFields(FormMapper $form)
  {
    $form
      ->add('slug')
      ->add('name')
    ;
  }

  protected function configureListFields(ListMapper $list)
  {
    $list
      ->add('name')
      ->add('_action', null, array(
        'actions' => array(
          'edit' => array(),
          'delete' => array()
        )
      ))
    ;
  }
}