<?php

namespace Tests\StoreBundle\Admin\Catalog;

use Tests\StoreBundle\Admin\AdminStoreWebTestCase;

class ProductAdminTest extends AdminStoreWebTestCase
{
  public function setUp()
  {
    parent::setUp();
  }

  /**
   * Кнопка "Скопировать" должна быть у каждого товара
   */
  public function testFindCopyButton()
  {
    $adminProductCatalog = $this->getClient()->getContainer()->get('main.admin.catalog.product');

    $url = $adminProductCatalog->generateUrl('list');

    $pattern = '[0-9]+';
    $needleUrl = $adminProductCatalog->generateUrl('clone', ['id'=>'REMOVE_ME']);
    $needleUrl = str_replace('/', '\/', $needleUrl);
    $needleUrl = str_replace('REMOVE_ME', $pattern, $needleUrl);

    $crawler = $this->getClient()->request('GET', $url);
    $rowsAtPage = $crawler->filter('td.sonata-ba-list-field.sonata-ba-list-field-batch')->count();

    $response = $this->getClient()->getResponse()->getContent();
    /** Проверим, что кнопок чтолько же, сколько товаров */
    $this->assertEquals($rowsAtPage, preg_match_all('/'.$needleUrl.'/', $response ));
  }
}