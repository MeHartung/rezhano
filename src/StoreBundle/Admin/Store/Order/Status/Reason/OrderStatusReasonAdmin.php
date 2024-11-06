<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 01.02.18
 * Time: 12:09
 */

namespace StoreBundle\Admin\Store\Order\Status\Reason;

use StoreBundle\Form\TinyMceType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class OrderStatusReasonAdmin extends AbstractAdmin
{

  public function configureListFields(ListMapper $list)
  {
    $list
      ->add('text', "html", ['truncate' => ['length' => 90]]) #тип для ограничения длины, 90 символов влазит без скролла
      ->add('_action', null, array(
        'actions' => [
          'edit' => [],
          'delete' => [],
        ]
      ));

  }

  public function configureFormFields(FormMapper $form)
  {
    $form
      ->add('text', TextareaType::class);
  }
}
