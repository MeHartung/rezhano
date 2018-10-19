<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 01.12.17
 * Time: 11:24
 */

namespace Tests\StoreBundle\Controller;


use StoreBundle\DataFixtures\OrderFixtures;
use StoreBundle\Entity\Store\Order\Order;
use Tests\StoreBundle\ExcamWebTestCase;

class CreditControllerTest extends ExcamWebTestCase
{

  public function setUp()
  {
    parent::setUp();
    $this->appendFixture(new OrderFixtures());
  }

  /**
   * https://jira.accurateweb.ru/browse/EXCAM-145 - после EXCAM-230 не актуально
   * https://jira.accurateweb.ru/browse/EXCAM-230
   */
  public function testTinkoff()
  {
    /** @var Order $order */
    $order = $this->getByReference('order-tinkoff');

    # Несуществующий заказ
    $this->client->request("GET", '/checkout/НОМЕРДОКУМЕНТА/complete/credit-tinkoff');
    $this->assertSame(404, $this->client->getResponse()->getStatusCode(),
                      "Такого заказа нет, но покупатель увидел что-то вместо 404");
    # Существующий заказ
   $crawler =  $this->client->request("GET", '/checkout/'.$order->getDocumentNumber().'/complete/credit-tinkoff');
    
    $this->assertSame(200, $this->client->getResponse()->getStatusCode(),
                      "Такого заказа нет, но покупатель увидел что-то вместо 404");
    
    # проверим кнопку
    $this->assertContains('Купить в кредит', $this->client->getResponse()->getContent(),
                         'Нет кнопки \"Купить в кредит\"');
  
   
    # проверим структуру формы
    $formHtmlSource = '
        <input name="shopId" value="test_online" type="hidden"/>
        <input name="sum" value="10000.00" type="hidden">
        <input name="itemName_0" value="Присоска GoPro Suction Cup" type="hidden"/>
        <input name="itemQuantity_0" value="2" type="hidden"/>
        <input name="itemPrice_0" value="5000.00" type="hidden"/>
        <input name="itemCategory_0" value="Присоска GoPro Suction Cup" type="hidden"/>
        <input name="customerEmail" value="customer@accurateweb.ru" type="hidden"/>
        <input name="customerPhone" value="" type="hidden"/>
        <input type="submit" value="Купить в кредит" formtarget="_blank" id="credit" />';
    
    $HtmlAsStr = preg_replace("/\s+/", '', $this->client->getResponse()->getContent());
    $formHtmlSource = preg_replace("/\s+/", '', $formHtmlSource);
    
    $this->assertContains($formHtmlSource, $HtmlAsStr, "Формы нет или её параметры невалидны");
  }

  /**
   * https://jira.accurateweb.ru/browse/EXCAM-146
   */
  public function testAlfa()
  {
    $order = $this->getByReference('order-alfa-bank');

    /**
     * Несуществующий заказ
     */
    $this->client->request("GET", '/checkout/НОМЕРДОКУМЕНТА/complete/credit-alfa-bank');
    $this->assertSame(404, $this->client->getResponse()->getStatusCode(), "Открыли заказ которого нет");

    /**
     * Смотрим, что реальный заказ не открывается, если его нет в сессии
     */
    $this->client->request("GET", '/checkout/' . $order->getDocumentNumber() . '/complete/credit-alfa-bank');
    $this->assertSame(404, $this->client->getResponse()->getStatusCode());
  }

}