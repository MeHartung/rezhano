<?php

namespace StoreBundle\Unit\Service\Order;

use StoreBundle\Service\Order\CartService;
use Tests\StoreBundle\StoreWebTestCase;

/**
 * @see CartService
 */
class CartServiceTest extends StoreWebTestCase
{
  public function testCreateCart()
  {
    $cart_service = $this->client->getContainer()->get('store.user.cart');
    $cart = $cart_service->getCart();
    $this->assertNull($cart->getCustomerFirstName(), 'Откуда в новой корзине взяться покупателю?');
  }

  /*
   * Аутентифицируемся до создания корзины
   */
  public function testFillByUser()
  {
    $cart_service = $this->client->getContainer()->get('store.user.cart');
    $this->logIn($this->getByReference('user-admin'));
    $cart = $cart_service->getCart();
    $this->assertSame('Админ', $cart->getCustomerFirstName(), 'Не заполнилась корзина данными покупателя');
  }

  /*
   * Аутентифицируемся после создания корзины
   */
  public function testFillByUserAfterCreateCart()
  {
    $cart_service = $this->client->getContainer()->get('store.user.cart');
    $cart_service->createCart();
    $this->logIn($this->getByReference('user-customer'));
    $cart = $cart_service->getCart();
    $this->assertSame('Иван', $cart->getCustomerFirstName(), 'Не заполнилась корзина данными покупателя');
  }
}