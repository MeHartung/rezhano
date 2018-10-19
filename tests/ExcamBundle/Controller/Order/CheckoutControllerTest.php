<?php

namespace Tests\StoreBundle\Controller\Order;

use AccurateCommerce\Shipping\Method\Excam\ShippingMethodExcamPickup;
use AccurateCommerce\Shipping\Method\Rupost\ShippingMethodRuPost;
use StoreBundle\Controller\Order\CheckoutController;
use StoreBundle\DataFixtures\OrderFixtures;
use StoreBundle\DataFixtures\Setting\SettingFixtures;
use StoreBundle\Entity\Store\Catalog\Product\Product;
use StoreBundle\Entity\Store\Order\Order;
use StoreBundle\Entity\Store\Payment\Method\PaymentMethod;
use Tests\StoreBundle\ExcamWebTestCase;

/**
 * @see CheckoutController
 */
class CheckoutControllerTest extends ExcamWebTestCase
{
  const ALFA_TYPE_GUID = '536591a3-7641-4afe-86b8-8fc5572fce58';
  const TINKOFF_TYPE_GUID = '2fe5f594-ddb8-4542-acda-e7b273df8e66';

  protected function setUp ()
  {
    parent::setUp();
    $this->appendFixture(new OrderFixtures());
    $this->appendFixture(new SettingFixtures());
  }

  /**
   * https://jira.accurateweb.ru/browse/EXCAM-208
   * https://jira.accurateweb.ru/browse/EXCAM-154
   */
  public function testCheckout()
  {
    $this->logIn();

    $this->client->request('GET', '/checkout');
    # после fe5aa68494a4339e807ef84ca962e8b27d1e850b не отдает 404, EXCAM-154
    $this->assertSame(200, $this->client->getResponse()->getStatusCode());

    $session = $this->client->getContainer()->get('session');
    $session->set('cart_id', 'order-free-delivery');
    $session->save();

    $crawler = $this->client->request('GET', '/checkout');
    $this->assertSame(200, $this->client->getResponse()->getStatusCode());

    $product_name = $crawler->filter('#basket_container .product a')->text();
    $product_quantity = $crawler->filter('#basket_container .inputbox_update')->attr('value');

    $this->assertSame('Радар-детектор Whistler WH 338ST RU', $product_name);
    $this->assertEquals(1, $product_quantity);

    $csrf = $this->client->getContainer()->get('session')->get('_csrf/checkout');

    $form_values = [
      'checkout' => [
        'customer_phone' => '+7 (959) 595-95-95',
        'customer_first_name' => 'TEST',
        'customer_last_name' => 'TEST',
        'customer_email' => 'test@accurateweb.ru',
        'shipping_city_name' => 'Екатеринбург',
        'shipping_post_code' => '620000',
        'shipping_address' => 'ул. Ленина д.1',
        'tos_agreement' => '1',
        'shipping_method_id' => ShippingMethodExcamPickup::UID,
        'payment_method' => $this->getByReference('payment-cash')->getId(),
        '_token' => $csrf
      ]
    ];

    $this->client->request('POST', '/checkout', $form_values);
    $this->assertSame(302, $this->client->getResponse()->getStatusCode(), 'Заказ не оформился');
    $this->client->followRedirect();

    /** @var Order $order */
    $order = $this->getByReference('order-free-delivery');
    $doc_no = $order->getDocumentNumber();

    $this->assertRegExp('/http:[s]?\/\/[^\/]*\/checkout\/'.preg_quote($doc_no).'\/complete/', $this->client->getRequest()->getUri());
    $this->assertSame($order->getCheckoutStateId(), Order::CHECKOUT_STATE_COMPLETE);

    # Проверим, что дефолт. статус сохранился в историю
    $this->assertSame(1, count($order->getOrderStatusHistory()));
  }

  /**
   * @see https://jira.accurateweb.ru/browse/EXCAM-205
   *
   * @throws \Doctrine\ORM\EntityNotFoundException
   */
  public function testPreOrder()
  {
    $this->logIn();

    /** @var Product $product */
    $product = $this->getByReference('product-preorder');
    $data = \json_encode([
      'product_slug' => $product->getSlug(),
      'phone' => '+7 (959) 559-59-95',
      'firstname' => 'Ghostbusters'
    ]);

    $this->client->request('POST', '/checkout/preorder', [], [], [], $data);
    $this->assertSame(200, $this->client->getResponse()->getStatusCode(), 'Предзаказ не создался');

    $last_order = $this->getEntityManager()->getRepository('StoreBundle:Store\Order\Order')
      ->findOneBy([], ['id' => 'DESC']);

    $this->assertSame(Order::CHECKOUT_STATE_PRE_ORDER, $last_order->getCheckoutStateId(), 'Не тот статус у заказа');
    $this->assertSame('+7 (959) 559-59-95', $last_order->getCustomerPhone(), 'Телефон не записался');
    $this->assertEquals($product->getPrice(), $last_order->getTotal(), 'Сумма не совпадает');
    $this->assertSame($this->user, $last_order->getUser());
    $this->assertEquals('Ghostbusters', $last_order->getCustomerFullName(),
      'Введенное ФИО покупателя не должно перебиваться значением из профиля');

    $data = \json_encode([
      'product_slug' => $product->getSlug(),
      'phone' => '+7 (959) 559-59-95',
      'firstname' => null
    ]);

    $this->client->request('POST', '/checkout/preorder', [], [], [], $data);
    $this->assertSame(400, $this->client->getResponse()->getStatusCode(), 'Нельзя создать предзаказ с пустым ФИО');
  }

  public function testOneClickOrder()
  {
    /** @var Product $product */
    $product = $this->getByReference('product-go-pro');
    $data = \json_encode([
      'product_slug' => $product->getSlug(),
      'phone' => '+7 (959) 999-99-99'
    ]);

    $this->client->request('POST', '/checkout/1click', [], [], [], $data);
    $this->assertSame(200, $this->client->getResponse()->getStatusCode(), 'Покупка в 1 клик не удалась');

    $last_order = $this->getEntityManager()->getRepository('StoreBundle:Store\Order\Order')
      ->findOneBy([], ['id' => 'DESC']);

    $this->assertSame(Order::CHECKOUT_STATE_ONE_CLICK, $last_order->getCheckoutStateId(), 'Не тот статус у заказа');
    $this->assertSame('+7 (959) 999-99-99', $last_order->getCustomerPhone(), 'Телефон не записался');
    $this->assertEquals($product->getPrice(), $last_order->getTotal(), 'Сумма не совпадает');
  }

  /**
   * Как покупатель, оформив заказ с опалтой в кредитом Альфа банк, я должен попасть на стр. с формой для
   * заполнения заявки на кредит
   * https://jira.accurateweb.ru/browse/EXCAM-146
   */
  public function testAlfaBankPaymentType()
  {
    $session = $this->client->getContainer()->get('session');
    $session->set('cart_id', 'order-free-delivery');
    $session->save();

    $crawler = $this->client->request('GET', '/checkout');
    $this->assertSame(200, $this->client->getResponse()->getStatusCode());

    $product_name = $crawler->filter('#basket_container .product a')->text();
    $product_quantity = $crawler->filter('#basket_container .inputbox_update')->attr('value');

    $this->assertSame('Радар-детектор Whistler WH 338ST RU', $product_name);
    $this->assertEquals(1, $product_quantity);

    $csrf = $this->client->getContainer()->get('session')->get('_csrf/checkout');

    $form_values = [
      'checkout' => [
        'customer_phone' => '+7 (959) 595-95-95',
        'customer_first_name' => 'TEST',
        'customer_last_name' => 'TEST',
        'customer_email' => 'test@accurateweb.ru',
        'shipping_city_name' => 'Екатеринбург',
        'shipping_post_code' => '620000',
        'shipping_address' => 'ул. Ленина д.1',
        'tos_agreement' => '1',
        'shipping_method_id' => ShippingMethodExcamPickup::UID,
        'payment_method' => $this->getByReference('payment-alfa')->getId(),
        '_token' => $csrf
      ]
    ];

    $this->client->request('POST', '/checkout', $form_values);
    $this->assertSame(302, $this->client->getResponse()->getStatusCode(), 'Заказ не оформился');
    $this->client->followRedirect();

    $this->assertSame(302, $this->client->getResponse()->getStatusCode(), 'Заказ не оформился');
    $orderRef = $this->getByReference('order-free-delivery');

    $order = $this->em->getRepository(Order::class)->find($orderRef->getId());

    $urlTo = '/checkout/' . $order->getDocumentNumber() . '/complete/credit-alfa-bank';
    $this->assertEquals($urlTo,  $this->client->getResponse()->getTargetUrl());
    $this->client->followRedirect();

    $this->assertSame(200, $this->client->getResponse()->getStatusCode(), 'Заказ не оформился');

    # Проверим тип оплаты заказа
    $this->assertEquals($this->getByReference('payment-alfa')->getId(), $order->getPaymentMethod()->getId());
  }

  /**
   * Как покупатель, оформив заказ с опалтой в кридат Тинькофф, я должен попасть на стр. с формой для
   * заполнения заявки на кредит
   * https://jira.accurateweb.ru/browse/EXCAM-145
   */
  public function testTinkoffPaymentType()
  {
    $session = $this->client->getContainer()->get('session');
    $session->set('cart_id', 'order-free-delivery');
    $session->save();

    $crawler = $this->client->request('GET', '/checkout');
    $this->assertSame(200, $this->client->getResponse()->getStatusCode());

    $product_name = $crawler->filter('#basket_container .product a')->text();
    $product_quantity = $crawler->filter('#basket_container .inputbox_update')->attr('value');

    $this->assertSame('Радар-детектор Whistler WH 338ST RU', $product_name);
    $this->assertEquals(1, $product_quantity);

    $csrf = $this->client->getContainer()->get('session')->get('_csrf/checkout');

    $form_values = [
      'checkout' => [
        'customer_phone' => '+7 (959) 595-95-95',
        'customer_first_name' => 'TEST',
        'customer_last_name' => 'TEST',
        'customer_email' => 'test@accurateweb.ru',
        'shipping_city_name' => 'Екатеринбург',
        'shipping_post_code' => '620000',
        'shipping_address' => 'ул. Ленина д.1',
        'tos_agreement' => '1',
        'shipping_method_id' => ShippingMethodExcamPickup::UID,
        'payment_method' => $this->getByReference('payment-tinkoff')->getId(),
        '_token' => $csrf
      ]
    ];

    $this->client->request('POST', '/checkout', $form_values);
    $this->assertSame(302, $this->client->getResponse()->getStatusCode(), 'Заказ не оформился');
    $this->client->followRedirect();

    $this->assertSame(302, $this->client->getResponse()->getStatusCode(), 'Заказ не оформился');
    $orderRef = $this->getByReference('order-free-delivery');

    $order = $this->em->getRepository(Order::class)->find($orderRef->getId());

    $urlTo = '/checkout/' . $order->getDocumentNumber() . '/complete/credit-tinkoff';
    $this->assertEquals($urlTo,  $this->client->getResponse()->getTargetUrl());
    $this->client->followRedirect();

    $this->assertSame(200, $this->client->getResponse()->getStatusCode(), 'Заказ не оформился');

    # Проверим тип оплаты заказа
    $this->assertEquals($this->getByReference('payment-tinkoff')->getId(), $order->getPaymentMethod()->getId());
  }

  public function testShippingMethodList()
  {
    $this->setCart('order-free-delivery');
    $this->client->request('GET', '/shipping/methods');
    $this->assertTrue($this->client->getResponse()->isOk());
    $data = \json_decode($this->client->getResponse()->getContent(), true);
    $this->assertCount(2, $data, 'Ожидали самовывоз + бесплатная доставка');
  }
  
  /**
   * Проверяем, что заказ с методом доставки "ПР" нельзя оформить за наличные без комиссии
   * https://jira.accurateweb.ru/browse/EXCAM-226
   */
  public function testRussianPostOrderDeliveryMethods()
  {
    $session = $this->client->getContainer()->get('session');
    $session->set('cart_id', 'order-in-cart');
    $session->save();
  
    $this->client->request('GET', '/checkout');
    # т.к. js тут не доступен, то нельзя проверить, что увидит пользователь,
    # но при нерабочем сервисе стоимость доставки = "Уточнит оператор"
    $this->assertSame(200, $this->client->getResponse()->getStatusCode());

    $csrf = $this->client->getContainer()->get('session')->get('_csrf/checkout');
    
    $form_values = [
      'checkout' => [
        'customer_phone' => '+7 (959) 595-95-95',
        'customer_first_name' => 'TEST',
        'customer_last_name' => 'TEST',
        'customer_email' => 'test@accurateweb.ru',
        'shipping_city_name' => 'Екатеринбург',
        'shipping_post_code' => '620000',
        'shipping_address' => 'ул. Ленина д.1',
        'tos_agreement' => '1',
        'shipping_method_id' => ShippingMethodRuPost::UID,
        'payment_method' => $this->getByReference('payment-cash')->getId(),
        '_token' => $csrf
      ]
    ];
  
    $this->client->request('POST', '/checkout', $form_values);
    $this->assertSame(302, $this->client->getResponse()->getStatusCode());
    /** @var Order $order */
    $order = $this->em->getRepository(Order::class)->findOneBy(['uid'=>'order-in-cart']);
    # проверяем сам заказ
    $this->assertSame(Order::CHECKOUT_STATE_COMPLETE, $order->getCheckoutStateId(), 'Заказ не сохранился');
    $this->assertTrue($order->getFee() !== null, 'Заказ сохранился без комиссии');
    $this->assertTrue($order->getFee() > 0, 'Комиссия при доставке наложенным платежом не м.б. 0');
    
    
  }
}