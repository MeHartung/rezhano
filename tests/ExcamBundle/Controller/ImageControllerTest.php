<?php
namespace Tests\StoreBundle\Controller;

/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 30.11.17
 * Time: 10:47
 */
use Doctrine\ORM\EntityManagerInterface;
use StoreBundle\DataFixtures\ImageFixtures;
use StoreBundle\Entity\Store\Catalog\Product\ProductImage;
use StoreBundle\Entity\User\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Tests\StoreBundle\ExcamWebTestCase;

/**
 *
 * Class ImageControllerTest
 * @package Tests\StoreBundle\Controller
 */
class ImageControllerTest extends ExcamWebTestCase
{
  public function setUp()
  {
    parent::setUp();
    $this->appendFixture(new ImageFixtures());
  }

  /**
   * Проверям, что если прислать в контроллер Id изображений,
   * который нужно поменять местами - он их поменяет.
   * https://jira.accurateweb.ru/browse/EXCAM-143
   */
  public function testMoveAction()
  {
    $this->login();

    $imageOne = $this->getByReference('product-with-image-img-0');
    $imageTwo = $this->getByReference('product-with-image-img-3');

    $this->client->request('POST', "/admin/api/image_move", [ 'position' => $imageTwo->getPosition(),
                                                                         'image_id'=>$imageOne->getId()]);

    #Проверим, что на валидные данные получим 200
    $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    #Проверим, что получили Json (json_decode в случае невалидного Json'a возращает null)
    $this->assertNotNull(json_decode($this->client->getResponse()->getContent()));

    /**
     * Невалид. данные
     */
    $this->client->request('POST', "/admin/api/image_move", [ 'position' => $imageTwo->getPosition(),
      'image_id'=>333]);

    $jsonResponse = json_decode($this->client->getResponse()->getContent(), true);

    # Сервер не свалился с 500
    $this->assertSame(400, $this->client->getResponse()->getStatusCode());
    # Нам пришел JSON
    $this->assertNotNull($jsonResponse);
    # В Json есть поле error
    $this->assertArrayHasKey('error', $jsonResponse);
  }
}