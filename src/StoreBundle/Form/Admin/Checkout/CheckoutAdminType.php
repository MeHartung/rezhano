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
 * Date: 26.10.2017
 * Time: 21:59
 */

namespace StoreBundle\Form\Admin\Checkout;

use AccurateCommerce\Shipping\ShippingManager;
use StoreBundle\Entity\Store\Order\Order;
use StoreBundle\Form\Checkout\CheckoutType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class CheckoutAdminType extends CheckoutType
{
  private $shippingManager;

  public function __construct(ShippingManager $shippingManager)
  {
    $this->shippingManager = $shippingManager;
  }

  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    parent::buildForm($builder, $options);

    $this->setFieldLabel($builder, 'customer_phone', 'Телефон покупателя');
    $this->setFieldLabel($builder, 'customer_last_name', 'Фамилия покупателя');
    $this->setFieldLabel($builder, 'customer_first_name', 'Имя покупателя');
    $this->setFieldLabel($builder, 'customer_email', 'Email покупателя');
    $this->setFieldLabel($builder, 'shipping_city_name', 'Город доставки');
    $this->setFieldLabel($builder, 'shipping_post_code', 'Почтовый индекс доставки');
    $this->setFieldLabel($builder, 'shipping_address', 'Адрес доставки');
    $this->setFieldLabel($builder, 'customer_comment', 'Комментарий покупателя');
    $this->setFieldLabel($builder, 'shipping_method', 'Способ доставки');
    $this->setFieldLabel($builder, 'payment_method', 'Способ оплаты');

    $builder->remove('tos_agreement');


    $builder
      ->add('shipping_method', ChoiceType::class, [
        'choices' => $this->getShippingChoices($options),
        'required' => true,
        'error_bubbling' => false,
        'constraints' => [
          new NotBlank(['message' => 'Пожалуйста, укажите способ доставки заказа']),
        ]
      ])
      ->add('shipping_cost', NumberType::class, array(
        'label' => 'Стоимость доставки',
        'required' => true
      ))
      ->add('fee', NumberType::class, array(
        'label' => 'Комиссия',
        'required' => true
      ));

    $builder->remove('submit');
  }

  private function setFieldLabel(FormBuilderInterface $builder, $fieldName, $label)
  {
    $this->mergeOptions($builder, $fieldName, array(
      'label' => $label
    ));
  }

  private function mergeOptions(FormBuilderInterface $builder, $fieldName, array $options = array())
  {
    $field = $builder->get($fieldName);

    $options = array_merge($field->getOptions(), $options);

    $builder->add($fieldName, $field->getType()->getInnerType()->getBlockPrefix(), $options);
  }

  private function getShippingChoices($options)
  {
    $choices = array();

    if(array_key_exists('data', $options) && $options['data'] instanceof Order)
    {
      $cityName = $options['data']->getShippingCityName();
    }

    $shippingMethods = $this->shippingManager->getShippingMethods();

    foreach ($shippingMethods as $shippingMethod)
    {
      $choices[$shippingMethod->getName($cityName)] = $shippingMethod->getUid();
    }

    return $choices;
  }
}