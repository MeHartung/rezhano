<?php
/**
 * (c) 2017 ИП Рагозин Денис Николаевич. Все права защищены.
 *
 * Настоящий файл является частью программного продукта, разработанного ИП Рагозиным Денисом Николаевичем
 * (ОГРНИП 315668300000095, ИНН 660902635476).
 *
 * Алгоритм и исходные коды программного кода программного продукта являются коммерческой тайной
 * ИП Рагозина Денис Николаевича. Любое их использование без согласия ИП Рагозина Денис Николаевича рассматривается,
 * как нарушение его авторских прав.
 *
 * Ответственность за нарушение авторских прав наступает в соответствии с действующим законодательством РФ.
 */

/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 27.10.2017
 * Time: 13:21
 */

namespace StoreBundle\Admin\Store\Order;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class OrderStatusAdmin extends AbstractAdmin
{
  protected function configureFormFields(FormMapper $form)
  {
    $form
      ->add('name')
      ->add('type', null, [
        'required' => true
      ])
      ->add('notificationTemplate', 'sonata_type_model', [
        'empty_data' => '',
        'placeholder' => '',

      ],
        [
          'link_parameters' => ['status_name' => $this->getSubject()->getName()]
        ]);
  }

  protected function configureListFields(ListMapper $list)
  {
    $list->addIdentifier('name')
      ->add('_action', null, [
        'actions' => [
          'edit' => [],
        ]]);
  }
}