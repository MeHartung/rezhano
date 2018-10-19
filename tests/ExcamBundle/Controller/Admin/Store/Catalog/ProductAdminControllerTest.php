<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 28.03.18
 * Time: 13:01
 */

namespace StoreBundle\Controller\Admin\Store\Catalog;


use StoreBundle\Entity\Store\Catalog\Product\Product;
use Tests\StoreBundle\ExcamWebTestCase;

class ProductAdminControllerTest extends ExcamWebTestCase
{
  public function setUp()
  {
    parent::setUp();
    $this->logIn();
  }

  /**
   * Проверим, что админ скопирует товар и увидит сообщение,
   * что всё прошло хорошо
   * https://jira.accurateweb.ru/browse/EXCAM-173
   */
  public function testCloneAction()
  {
    /** @var Product $prdct */
    $prdct = $this->getByReference('product-go-pro');

    $url = $this->client->getContainer()->get('main.admin.catalog.product')
                                        ->generateUrl('clone', ['id'=>$prdct->getId()]);

    $this->client->request('GET', $url);
    $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    $crawler = $this->client->followRedirect();
    $this->assertEquals(200, $this->client->getResponse()->getStatusCode(),
                        "При копировании товара произошла ошибка");

    $sonataFlashText = $crawler->filter(".alert.alert-success.alert-dismissable")->text();
    $sonataFlashText = trim(str_replace('×', '', $sonataFlashText));
    # Проверяем, что товар скопиролвался успешно, пользователь это увидел
    $this->assertEquals("Экшн-камера GoPro HERO Session успешно скопирован.", $sonataFlashText,
                         "Товар не был скопирован.");
  }

}