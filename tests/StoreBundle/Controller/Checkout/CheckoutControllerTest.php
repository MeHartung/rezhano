<?php

namespace Tests\StoreBundle\Controller\Checkout;

use PHPUnit\Framework\MockObject\MockObject;
use StoreBundle\Entity\Store\Order\Order;
use StoreBundle\Service\Order\CartService;
use Tests\DataFixtures\Catalog\ProductFixture;
use Tests\DataFixtures\Logistic\CdekCityFixture;
use Tests\DataFixtures\Logistic\ProductStockFixture;
use Tests\DataFixtures\Logistic\WarehouseFixture;
use Tests\DataFixtures\Order\OrderFixture;
use Tests\DataFixtures\PaymentMethodFixture;
use Tests\DataFixtures\Taxon\TaxonFixture;
use Tests\StoreBundle\StoreWebTestCase;

class CheckoutControllerTest extends StoreWebTestCase
{
  protected function setUp ()
  {
    parent::setUp();
    $this->appendFixture(new TaxonFixture());
    $this->appendFixture(new ProductFixture());
    $this->appendFixture(new CdekCityFixture());
    $this->appendFixture(new WarehouseFixture());
    $this->appendFixture(new ProductStockFixture());
    $this->appendFixture(new PaymentMethodFixture());
    $this->appendFixture(new OrderFixture());
  }

  public function testDeliveryStepPickupAction ()
  {
    /** @var Order $cart */
    $cart = $this->getReference('order-in-cart');
    /** @var MockObject|CartService $cartService */
    $cartService = $this->getMockBuilder('StoreBundle\Service\Order\CartService')->disableOriginalConstructor()->getMock();
    $csrfTokenManager = $this->getMockBuilder('Symfony\Component\Security\Csrf\CsrfTokenManagerInterface')->getMock();
    $csrfTokenManager->method('isTokenValid')->willReturn(true);
    /*
     * Отключаем ребут, чтобы не сбрасывались моки
     */
    $this->getClient()->disableReboot();
    $this->getClient()->getContainer()->set('store.user.cart', $cartService);
    $this->getClient()->getContainer()->set('security.csrf.token_manager', $csrfTokenManager);

    /*
     * На первый вызов пустую корзину, чтобы проверить редирект
     */
    $cartService
      ->expects($this->at(0))
      ->method('getCart')->willReturn(new Order());
    $cartService
      ->expects($this->any())
      ->method('getCart')->willReturn($cart);

    /*
     * Неавторизованный
     */
    $this->getClient()->request('GET', '/checkout/delivery');
    $this->assertSame(302, $this->getClient()->getResponse()->getStatusCode(), 'Должно отправить авторизовываться');
    $this->assertRegExp('/\/login$/', $this->getClient()->getResponse()->headers->get('Location'));

    /*
     * Авторизованный, но без корзины
     */
    $this->logIn();
    $this->getClient()->request('GET', '/checkout/delivery');
    $this->assertSame(302, $this->getClient()->getResponse()->getStatusCode(), 'Должно отправить в корзину, т.к. она пустая');
    $this->assertRegExp('/\/cart/', $this->getClient()->getResponse()->headers->get('Location'));

    /*
     * Авторизованный с корзиной, получаем форму
     */
    $crawler = $this->getClient()->request('GET', '/checkout/delivery');
    $this->assertSame(200, $this->getClient()->getResponse()->getStatusCode());

    /*
     * Отправляем форму
     */
    $form = $crawler->filter('form[name=typePickup]')->form();
    $this->logIn();

    $data = [
      'typePickup' => [
//        'shippingAddress' => 'Lenina 26',
        'shippingDate' => (new \DateTime('tomorrow'))->format('d.m.Y'),
        'shippingMethodId' => '8dc7ee8f-18f0-40af-964f-d10c3ab091a3',
        '_token' => 'validToken',
      ]
    ];

    $this->getClient()->submit($form, $data);
    $this->assertSame(302, $this->getClient()->getResponse()->getStatusCode());
    $this->assertRegExp('/\/checkout\/payment/', $this->getClient()->getResponse()->headers->get('Location'));
  }

  public function testDeliveryStepCourierAction ()
  {
    /** @var Order $cart */
    $cart = $this->getReference('order-in-cart');
    /** @var MockObject|CartService $cartService */
    $cartService = $this->getMockBuilder('StoreBundle\Service\Order\CartService')->disableOriginalConstructor()->getMock();
    $csrfTokenManager = $this->getMockBuilder('Symfony\Component\Security\Csrf\CsrfTokenManagerInterface')->getMock();
    $csrfTokenManager->method('isTokenValid')->willReturn(true);
    /*
     * Отключаем ребут, чтобы не сбрасывались моки
     */
    $this->getClient()->disableReboot();
    $this->getClient()->getContainer()->set('store.user.cart', $cartService);
    $this->getClient()->getContainer()->set('security.csrf.token_manager', $csrfTokenManager);

    /*
     * На первый вызов пустую корзину, чтобы проверить редирект
     */
    $cartService
      ->expects($this->at(0))
      ->method('getCart')->willReturn(new Order());
    $cartService
      ->expects($this->any())
      ->method('getCart')->willReturn($cart);

    /*
     * Неавторизованный
     */
    $this->getClient()->request('GET', '/checkout/delivery');
    $this->assertSame(302, $this->getClient()->getResponse()->getStatusCode(), 'Должно отправить авторизовываться');
    $this->assertRegExp('/\/login$/', $this->getClient()->getResponse()->headers->get('Location'));

    /*
     * Авторизованный, но без корзины
     */
    $this->logIn();
    $this->getClient()->request('GET', '/checkout/delivery');
    $this->assertSame(302, $this->getClient()->getResponse()->getStatusCode(), 'Должно отправить в корзину, т.к. она пустая');
    $this->assertRegExp('/\/cart/', $this->getClient()->getResponse()->headers->get('Location'));

    /*
     * Авторизованный с корзиной, получаем форму
     */
    $crawler = $this->getClient()->request('GET', '/checkout/delivery');
    $this->assertSame(200, $this->getClient()->getResponse()->getStatusCode());

    /*
     * Отправляем форму
     */
    $form = $crawler->filter('form[name=typeCourier]')->form();
    $this->logIn();

    $data = [
      'typeCourier' => [
        'shippingAddress' => 'Lenina 26',
        'shippingDate' => (new \DateTime('tomorrow'))->format('d.m.Y'),
        'shippingMethodId' => 'eac20e0f-056a-4c10-9f43-7bee5c47167a',
        '_token' => 'validToken',
      ]
    ];

    $this->getClient()->submit($form, $data);
    $this->assertSame(302, $this->getClient()->getResponse()->getStatusCode());
    $this->assertRegExp('/\/checkout\/payment/', $this->getClient()->getResponse()->headers->get('Location'));
  }

  public function testDeliveryStepXhr()
  {
    /** @var Order $cart */
    $cart = $this->getReference('order-in-cart');
    /** @var MockObject|CartService $cartService */
    $cartService = $this->getMockBuilder('StoreBundle\Service\Order\CartService')->disableOriginalConstructor()->getMock();
    $cartService->method('getCart')->willReturn($cart);
    /*
     * Отключаем ребут, чтобы не сбрасывались моки
     */
    $this->getClient()->disableReboot();
    $this->getClient()->getContainer()->set('store.user.cart', $cartService);
    $this->logIn();

    $data = [
      'shippingAddress' => 'Lenina 26',
      'shippingDate' => (new \DateTime('tomorrow'))->format('d.m.Y'),
      'shippingMethodId' => 'eac20e0f-056a-4c10-9f43-7bee5c47167a',
    ];

    $this->getClient()->request('POST','/checkout/delivery', [], [], [
      'HTTP_X-Requested-With' => 'XMLHttpRequest',
    ], json_encode($data));

    $this->assertSame(200, $this->getClient()->getResponse()->getStatusCode());
  }

  public function testPaymentStepAction ()
  {
    /** @var Order $cart */
    $cart = $this->getReference('order-in-delivery');
    /** @var MockObject|CartService $cartService */
    $cartService = $this->getMockBuilder('StoreBundle\Service\Order\CartService')->disableOriginalConstructor()->getMock();
    $csrfTokenManager = $this->getMockBuilder('Symfony\Component\Security\Csrf\CsrfTokenManagerInterface')->getMock();
    $csrfTokenManager->method('isTokenValid')->willReturn(true);
    /*
     * Отключаем ребут, чтобы не сбрасывались моки
     */
    $this->getClient()->disableReboot();
    $this->getClient()->getContainer()->set('store.user.cart', $cartService);
    $this->getClient()->getContainer()->set('security.csrf.token_manager', $csrfTokenManager);

    /*
     * На первый вызов пустую корзину, чтобы проверить редирект
     */
    $cartService
      ->expects($this->at(0))
      ->method('getCart')->willReturn(new Order());
    $cartService
      ->expects($this->any())
      ->method('getCart')->willReturn($cart);

    /*
     * Неавторизованный
     */
    $this->getClient()->request('GET', '/checkout/payment');
    $this->assertSame(302, $this->getClient()->getResponse()->getStatusCode(), 'Должно отправить авторизовываться');
    $this->assertRegExp('/\/login$/', $this->getClient()->getResponse()->headers->get('Location'));

    /*
     * Авторизованный, но без корзины
     */
    $this->logIn();
    $this->getClient()->request('GET', '/checkout/payment');
    $this->assertSame(302, $this->getClient()->getResponse()->getStatusCode(), 'Должно отправить в корзину, т.к. она пустая');
    $this->assertRegExp('/\/cart/', $this->getClient()->getResponse()->headers->get('Location'));

    /*
     * Авторизованный с корзиной, получаем форму
     */
    $crawler = $this->getClient()->request('GET', '/checkout/payment');
    $this->assertSame(200, $this->getClient()->getResponse()->getStatusCode());

    /*
     * Отправляем форму
     */
    $form = $crawler->filter('form[name=payment_step]')->form();
    $this->logIn();

    $data = [
      'payment_step' => [
        'customerPhone' => '+7 (952) 732-56-58',
        'customerFirstName' => 'Customer',
        'paymentMethod' => $this->getReference('payment-cash')->getId(),
        '_token' => 'validToken',
      ]
    ];

    $this->getClient()->submit($form, $data);
    $this->assertSame(302, $this->getClient()->getResponse()->getStatusCode());
    $this->assertRegExp('/\/checkout\/[^\/]+\/complete/', $this->getClient()->getResponse()->headers->get('Location'));
  }
}