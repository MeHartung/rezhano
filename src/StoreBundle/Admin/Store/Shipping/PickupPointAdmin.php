<?php
/**
 * Created by PhpStorm.
 * User: eobuh
 * Date: 26.10.2018
 * Time: 12:41
 */

namespace StoreBundle\Admin\Store\Shipping;


use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class PickupPointAdmin extends AbstractAdmin
{
  public function configureListFields(ListMapper $list)
  {
    $list
      ->add('address');
  }
  
  public function configureFormFields(FormMapper $form)
  {
    $form
      ->add('address')
      ->add('name')
      ->add('description')
      ;
  }
}