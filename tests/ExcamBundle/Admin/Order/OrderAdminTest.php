<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 12.02.18
 * Time: 16:27
 */

namespace StoreBundle\Admin\Order;


use StoreBundle\DataFixtures\Order\OrderFilterAdminTestFixtures;
use StoreBundle\DataFixtures\OrderFixtures;
use StoreBundle\DataFixtures\OrderStatusFixture;
use StoreBundle\DataFixtures\ProductFixture;
use StoreBundle\Entity\Store\Catalog\Product\Product;
use StoreBundle\Entity\Store\Order\Order;
use StoreBundle\Entity\Store\Order\Status\OrderStatusHistory;
use StoreBundle\Entity\User\User;
use Tests\StoreBundle\StoreWebTestCase;

class OrderAdminTest extends StoreWebTestCase
{
  public function setUp()
  {
    parent::setUp();
    $this->appendFixture(new OrderFixtures());
  }

  /**
   * Проверим, что кнопка "отменить заказ" есть у каждого заказа
   * https://jira.accurateweb.ru/browse/EXCAM-177
   */
  public function testFindCancelButton()
  {
    $adminProductCatalog = $this->client->getContainer()->get('main.admin.order');

    $url = $adminProductCatalog->generateUrl('list');

    $pattern = '[0-9]+';
    $needleUrl = $adminProductCatalog->generateUrl('cancel', ['id' => 'REMOVE_ME']);
    $needleUrl = str_replace('/', '\/', $needleUrl);
    $needleUrl = str_replace('REMOVE_ME', $pattern, $needleUrl);

    $crawler = $this->client->request('GET', $url);
    $rowsAtPage = $crawler->filter('td.sonata-ba-list-field.sonata-ba-list-field-batch')->count();

    $response = $this->client->getResponse()->getContent();
    $this->assertEquals($rowsAtPage, preg_match_all('/' . $needleUrl . '/', $response));
  }

  /**
   * Проверим, что админ, отредактировав заказ, не изменит user_id
   * https://jira.accurateweb.ru/browse/EXCAM-147
   */
  public function testAdminNotTakeUserOrder()
  {
    $order = $this->getByReference('order-one-click');

    $admin = $this->getByReference('user-admin');
    $this->logIn($admin, [User::ROLE_SUPER_ADMIN]);

    $url = $this->client->getContainer()
      ->get('main.admin.order')
      ->generateUrl('checkout', ['id' => $order->getId()]);

    $crawler = $this->client->request('GET', $url);
    $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

    $form = $crawler->filter('.btn.btn-success')->form(
      [
        'checkout_admin[shipping_cost]' => '123',
        'checkout_admin[fee]' => '123',
        'checkout_admin[customer_phone]' => '+79128529985',
        'checkout_admin[customer_email]' => 'q@123.cv',
        'checkout_admin[customer_last_name]' => '123',
        'checkout_admin[customer_first_name]' => '123',
        'checkout_admin[shipping_method_id]' => 'ecc4f177-526e-471f-8f37-5608f1ca86bc',
        'checkout_admin[shipping_city_name]' => 'Туда',
      ]
    );

    $this->client->submit($form);

    /**
     * Редиректит?
     */
    $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    $this->client->followRedirect();
    /**
     * Лист не сломался
     */
    $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

    /**
     * Админ не стал влдельцем заказа?
     */
    $this->assertNotEquals($order->getUser()->getId(), $admin->getId(), 'Адвин сделал заказ своим, отредактировав его.');
  }

  /**
   * Проверим, что админ может изменить статус оплаты заказа
   * @test https://jira.accurateweb.ru/browse/EXCAM-197
   */
  public function testPaymentStatus()
  {
    $this->logIn();
    $orderRepo = $this->em->getRepository(Order::class);

    $order = $this->getByReference('order');
    $orderStatus = $this->getByReference('order-status-processing');
    /**
     * Ставим тип оплачен
     */
    $link = sprintf("/admin/Store/store-order-order/%s/edit", $order->getId());
    $crawler = $this->client->request('GET', $link);

    $tokenSonata = str_replace('_document_number', '',
      $crawler->filter('.control-label')->eq(0)->attr('for'));

    $token = $crawler->filter('#' . $tokenSonata . '__token')->attr('value');

    $adminFormData =
      [
        $tokenSonata . '[customer_first_name]' => 'asd',
        $tokenSonata . '[customer_last_name]' => 'asd',
        $tokenSonata . '[customer_phone]' => '+7 (989) 898-98-98',
        $tokenSonata . '[customer_email]' => 'admin@localhost.tu',
        $tokenSonata . '[payment_method]' => '1',
        $tokenSonata . '[shipping_method_id]' => '8dc7ee8f-18f0-40af-964f-d10c3ab091a3',
        $tokenSonata . '[shipping_city_name]' => 'Москва',
        $tokenSonata . '[shipping_postcode]' => '101000',
        $tokenSonata . '[shipping_address]' => 'sdfk',
        $tokenSonata . '[customer_comment]' => 'asd',
        $tokenSonata . '[shippingCost]' => 100,
        $tokenSonata . '[fee]' => 100,
        $tokenSonata . '[paymentStatus]' => $orderStatus->getId(),
        $tokenSonata . '[_token]' => $token
      ];

    $form = $crawler->filter(".btn.btn-success")->first()->form($adminFormData);
    $this->client->submit($form);
    $this->em->clear();

    /**
     * Проверим, что значение статуса оплаты поменнялось
     */
    $order = $orderRepo->find($order->getId());
    $this->assertEquals(1, $order->getPaymentStatus()->getId(), "Статус оплаты заказа не изменился.");
    $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    $this->client->followRedirect();
    $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
  }

  /**
   * https://jira.accurateweb.ru/browse/EXCAM-208
   */
  public function testOnlyActiveOrdersFilter()
  {
    $this->appendFixture(new OrderFilterAdminTestFixtures());
    $this->appendFixture(new OrderStatusFixture());
    $this->logIn();

    $filter = [
      'filter' =>
        [
          'status' =>
            [
              'value' => 1
            ]
        ]
    ];

    $crawler = $this->client->request('GET', '/admin/Store/store-order-order/list');
    $filterForm = $crawler->filter(".btn.btn-primary")->form($filter);

    /**
     * Проверим, что ни одного заказа не получили (нет у них статусов)
     */
    $crawler = $this->client->submit($filterForm);
    $this->assertEquals(0, $crawler->filter(".fa.fa-cart-plus")->count());

    /** @var Order $order */
    $order = $this->getByReference('order-for-sort-1');
    $order->setOrderStatus($this->getByReference('order-status-processing'));
    $this->em->persist($order);
    $this->em->flush();

    $crawler = $this->client->submit($filterForm);
    # Проверим, что когда статус заказа сменился на активный - мы получили его в выдаче
    $this->assertEquals(1, $crawler->filter(".fa.fa-cart-plus")->count());
    # Проверим, что мы получили именно тот заказ, которму проставили активный статус
    $btnHref = $crawler->filter('.btn.btn-sm.btn-default.edit_link')->attr('href');
    $pathHref = sprintf("/%s/edit", $this->getByReference('order-for-sort-1')->getId());
    $this->assertContains($pathHref, $btnHref);

    # проставим статус с типо завершён
    $order = $this->getByReference('order-for-sort-1');
    $order->setOrderStatus($this->getByReference('order-status-cancel'));
    $this->em->persist($order);
    $this->em->flush();

    $crawler = $this->client->submit($filterForm);
    # Проверим, что когда статус заказа сменился на завершённый, он не появился в выдаче
    $this->assertEquals(0, $crawler->filter(".fa.fa-cart-plus")->count());
  }

  /**
   * https://jira.accurateweb.ru/browse/EXCAM-208
   */
  public function testOrderStatusHistory()
  {
    $this->logIn();
    /** @var User $usr */
    $usr = $this->getByReference('user-admin');
    #Как админ я сменю статус заказу
    $adminProductCatalog = $this->client->getContainer()->get('main.admin.order');
    $order = $this->getByReference('order');
    $status = $this->getByReference('order-status-processing');

    $needleUrl = $adminProductCatalog->generateUrl('status', ['id' => $order->getId()]);
    $crawler = $this->client->request('GET', $needleUrl);

    $form = $crawler->filter('.btn.btn-success')->form(
      [
        'status_admin[status]' => $status->getId(),
        'status_admin[notification]' => false,
        'status_admin[reason]' => 'Reason',
      ]);

    $this->client->submit($form);

    $this->client->request("GET", "/admin/Store/store-order-order/".$order->getId()."/orderStatusHistory");
    $response = $this->client->getResponse()->getContent();
    $this->assertContains($status->getName(), $response);
    $this->assertContains($usr->getFio(), $response);

  }

}