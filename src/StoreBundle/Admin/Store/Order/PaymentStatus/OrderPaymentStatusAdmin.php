<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 13.03.18
 * Time: 16:19
 */

namespace StoreBundle\Admin\Store\Order\PaymentStatus;


use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class OrderPaymentStatusAdmin extends AbstractAdmin
{

  public function configureListFields(ListMapper $list)
  {
    $list
      ->add('name')
      ->add('type')
      ->add('_action', null, [
        'actions' => [
          'edit' => [],
        ]]);
  }

  public function configureFormFields(FormMapper $form)
  {
    $form
      ->add('name')
      ->add('type', null, ['required' => true]);
  }


}