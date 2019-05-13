<?php

namespace Tests\StoreBundle\Unit\EventListener\EmailMessaging;


use StoreBundle\Entity\Store\Order\Order;
use StoreBundle\EventListener\EmailMessaging\OrderCheckoutCustomerMail;
use Tests\DataFixtures\Catalog\Product\Attribute\ProductTypeFixture;
use Tests\DataFixtures\Catalog\ProductFixture;
use Tests\DataFixtures\Order\OrderFixture;
use Tests\DataFixtures\PaymentMethodFixture;
use Tests\DataFixtures\Store\ShippingMethodFixture;
use Tests\DataFixtures\Taxon\TaxonFixture;
use Tests\StoreBundle\StoreWebTestCase;

class OrderCheckoutCustomerMailTest extends StoreWebTestCase
{
  protected function setUp()
  {
    parent::setUp();
    $this->addFixture(new ShippingMethodFixture());
    $this->addFixture(new PaymentMethodFixture());
    $this->addFixture(new TaxonFixture());
    $this->addFixture(new ProductTypeFixture());
    $this->addFixture(new ProductFixture());
    $this->addFixture(new OrderFixture);
    $this->executeFixtures();
  }
  
  /**
   * Проверяет, что в письме правильно форматируется вес позиции заказа
   *
   * @throws \ReflectionException
   */
  public function testGetEmailVariabless()
  {
    $container = $this->client->getContainer();
    $getEmailVariables = static::getMethod('getEmailVariables');
    $class = new OrderCheckoutCustomerMail(
      $container->get('mailer'),
      $container->get('logger'),
      $container->get('aw_email_templating.template.factory'),
      $container->get('accurateweb.shipping.manager'),
      'mail@sobaka.ururu',
      'mail@sobaka.ururu',
      $container->get('twig')
    );
    /** @var Order $order */
    $order = $this->getReference('order-all-types-products');
    
    $result = $getEmailVariables->invokeArgs($class, [$order]);
    
    $this->assertContains('450 г', $result['order_items']);
    $this->assertContains('1 шт', $result['order_items']);
    $this->assertContains('1,2 кг', $result['order_items']);
  }
  
  /**
   * @param string $methodName
   * @return \ReflectionMethod
   * @throws \ReflectionException
   */
  protected static function getMethod(string $methodName): \ReflectionMethod
  {
    $class = new \ReflectionClass(OrderCheckoutCustomerMail::class);
    $method = $class->getMethod($methodName);
    $method->setAccessible(true);
    
    return $method;
  }
}