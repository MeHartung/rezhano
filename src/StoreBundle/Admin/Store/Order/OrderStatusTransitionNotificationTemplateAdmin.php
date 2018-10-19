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
 * Time: 13:26
 */

namespace StoreBundle\Admin\Store\Order;

use StoreBundle\Form\TinyMceType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class OrderStatusTransitionNotificationTemplateAdmin extends AbstractAdmin
{
  protected function configureListFields(ListMapper $list)
  {
    $list
      ->add('title', null, ["label" => "Название"]) # ибо стандартный перевод отличается
    ->add('_action', null, array(
       'actions' => [
         'edit' => [],
       ]
     ));
  }


  protected function configureFormFields(FormMapper $form)
  {
    $form
      ->with('Уведомления о смене статусов заказов ', array(
        'description' => '<label class="control-label">
                            Доступные переменные
                          </label>
                          <table class="supported-variable-list table table-sm" style="width:50%; margin: 0 auto;">
          <tbody><tr>
        <td>%customer_name%</td>
        <td>ФИО покупателя</td>
      </tr>
      <tr>
        <td>%order_status%</td>
        <td>Статус заказа</td>
      </tr>
          <tr>
        <td>%order_number%</td>
        <td>Номер заказа</td>
      </tr>
          <tr>
        <td>%customer_phone%</td>
        <td>Телефон покупателя</td>
      </tr>
          <tr>
        <td>%customer_email%</td>
        <td>Email покупателя</td>
      </tr>
          <tr>
        <td>%payment_method%</td>
        <td>Способ оплаты</td>
      </tr>
          <tr>
        <td>%shipping_method%</td>
        <td>Способ доставки</td>
      </tr>
          <tr>
        <td>%shipping_address%</td>
        <td>Адрес доставки (включая город и индекс)</td>
      </tr>
          <tr>
        <td>%subtotal%</td>
        <td>Стоимость товаров в заказе</td>
      </tr>
          <tr>
        <td>%shipping_cost%</td>
        <td>Стоимость доставки</td>
      </tr>
          <tr>
        <td>%fee%</td>
        <td>Комиссия</td>
      </tr>
          <tr>
        <td>%total%</td>
        <td>Стоимость заказа</td>
      </tr>
      <tr>
        <td>%order_items%</td>
        <td>Состав заказа</td>
      </tr>
      </tbody></table>'
      ))
      ->add('title', null, ["label" => "Название"])
      ->add('subject', TextType::class, array('label' => 'Шаблон темы письма'))
      ->add('body', TinyMceType::class, array('label' => 'Шаблон тела письма'))
    ->end();
  }
}