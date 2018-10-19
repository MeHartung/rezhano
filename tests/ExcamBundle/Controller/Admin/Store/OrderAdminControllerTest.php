<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 28.03.18
 * Time: 12:10
 */

namespace StoreBundle\Controller\Admin\Store;


use StoreBundle\DataFixtures\OrderFixtures;
use StoreBundle\DataFixtures\OrderStatusFixture;
use StoreBundle\Entity\Store\Order\Order;
use StoreBundle\Entity\Store\Order\Status\OrderOrderStatus;
use Tests\StoreBundle\ExcamWebTestCase;

class OrderAdminControllerTest extends ExcamWebTestCase
{
  public function setUp()
  {
    parent::setUp();
    $this->appendFixture(new OrderFixtures());
    $this->appendFixture(new OrderStatusFixture());
    $this->logIn();
  }

  /**
   * Админ может изменть статус заказа
   * https://jira.accurateweb.ru/browse/EXCAM-125
   * https://jira.accurateweb.ru/browse/EXCAM-123
   */
  public function testStatusAction()
  {
    $adminProductCatalog = $this->client->getContainer()->get('main.admin.order');
    /** @var Order $order */
    $order = $this->getByReference('order');
    $status = $this->getByReference('order-status-processing');

    $needleUrl = $adminProductCatalog->generateUrl('status', ['id' => $order->getId()]);
    $crawler = $this->client->request('GET', $needleUrl);
    $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

    $form = $crawler->filter('.btn.btn-success')->form(
      [
        'status_admin[status]' => $status->getId(),
        'status_admin[notification]' => false,
        'status_admin[reason]' => 'Reason',
      ]);

    $this->client->submit($form);

    $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    $this->client->followRedirect();
    $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Сломался лист товаров.");

    $this->assertEquals($order->getLastOrderStatusHistory()->getId(), $status->getId(), "Статус заказа не изменился.");
  }
}