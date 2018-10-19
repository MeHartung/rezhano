<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 08.02.18
 * Time: 12:44
 */

namespace StoreBundle\Admin\Order\Product;

use StoreBundle\Entity\Store\Catalog\Product\Product;
use StoreBundle\Entity\User\User;
use Tests\StoreBundle\ExcamWebTestCase;

class ProductAdminTest extends ExcamWebTestCase
{
  public function setUp()
  {
    parent::setUp();
  }

  /**
   * Кнопка "Скопировать" должна быть у каждого товара
   * https://jira.accurateweb.ru/browse/EXCAM-173
   */
  public function testFindCopyButton()
  {
    $user = $this->getByReference('user-admin');
    $this->logIn($user, [User::ROLE_SUPER_ADMIN]);

    $adminProductCatalog = $this->client->getContainer()->get('main.admin.catalog.product');

    $url = $adminProductCatalog->generateUrl('list');

    $pattern = '[0-9]+';
    $needleUrl = $adminProductCatalog->generateUrl('clone', ['id'=>'REMOVE_ME']);
    $needleUrl = str_replace('/', '\/', $needleUrl);
    $needleUrl = str_replace('REMOVE_ME', $pattern, $needleUrl);

    $crawler = $this->client->request('GET', $url);
    $rowsAtPage = $crawler->filter('td.sonata-ba-list-field.sonata-ba-list-field-batch')->count();

    $response = $this->client->getResponse()->getContent();
    /** Проверим, что кнопок чтолько же, сколько товаров */
    $this->assertEquals($rowsAtPage, preg_match_all('/'.$needleUrl.'/', $response ));
  }
}