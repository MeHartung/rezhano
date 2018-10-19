<?php

namespace Tests\StoreBundle\Unit\Form\Checkout;

use AccurateCommerce\Shipping\Method\Excam\ShippingMethodExcamPickup;
use StoreBundle\DataFixtures\OrderFixtures;
use StoreBundle\Entity\Store\Order\Order;
use StoreBundle\Form\Checkout\CheckoutType;
use Symfony\Component\Form\FormFactoryInterface;
use Tests\StoreBundle\ExcamWebTestCase;

class CheckoutTypeTest extends ExcamWebTestCase
{
  /**
   * @var FormFactoryInterface
   */
  protected $factory;

  protected function setUp()
  {
    parent::setUp();
    $this->factory = $this->client->getContainer()->get('form.factory');
    $this->appendFixture(new OrderFixtures());
  }

  /**
   * Корзина, в которую случайно попал товар, доступный только для предзаказа, не должна быть оформлена
   * @link https://jira.accurateweb.ru/browse/EXCAM-184
   */
  public function testPreorderItems()
  {
    /** @var Order $order */
    $order = $this->getByReference('order-preorder');
    $order->setCheckoutStateId(Order::CHECKOUT_STATE_CART);

    $form = $this->factory->create(CheckoutType::class, $order, ['csrf_protection' => false]);

    $data = [
      'customer_phone' => '+7 (959) 595-95-95',
      'customer_first_name' => 'TEST',
      'customer_last_name' => 'TEST',
      'customer_email' => 'test@accurateweb.ru',
      'shipping_city_name' => 'Екатеринбург',
      'shipping_post_code' => '620000',
      'shipping_address' => 'ул. Ленина д.1',
      'tos_agreement' => '1',
      'shipping_method_id' => ShippingMethodExcamPickup::UID,
      'payment_method' => $this->getByReference('payment-cash')->getId()
    ];

    $form->submit($data);

    $this->assertFalse($form->isValid(), 'Форма не должна быть валидной');
    $field = $form->get('orderItems');

    $this->assertCount(1, $field->getErrors(), 'Ошибка не там, где ждали');
  }
}