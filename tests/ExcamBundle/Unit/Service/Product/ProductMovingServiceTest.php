<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 28.03.18
 * Time: 14:20
 */

namespace StoreBundle\Unit\Service\Product;


use Accurateweb\MediaBundle\Model\Image\Image;
use StoreBundle\DataFixtures\ImageFixtures;
use StoreBundle\Entity\Store\Catalog\Product\Product;
use StoreBundle\Entity\Store\Catalog\Product\ProductImage;
use StoreBundle\Service\Product\ProductMovingService;
use PHPUnit\Runner\Exception;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\StoreBundle\ExcamWebTestCase;

class ProductMovingServiceTest extends ExcamWebTestCase
{
  /**
   * @var ProductMovingService
   */
  private $prdctMoveService;

  public function setUp()
  {
    parent::setUp();
    $this->appendFixture(new ImageFixtures());
    $this->prdctMoveService = $this->client->getContainer()->get('store.product.image.move');
  }

  /**
   * Проверям, что перемещение изображ. работатет
   * https://jira.accurateweb.ru/browse/EXCAM-143
   */
  public function testImgMove()
  {
    $imageOne = $this->getByReference('product-with-image-img-0');
    $imageTwo = $this->getByReference('product-with-image-img-3');

    try
    {
      $this->prdctMoveService->moveImage(null, null);
    }
    catch (\Exception $exception)
    {
      $this->assertEquals('Не найдено перемещаемое избражение', $exception->getMessage());
    }

    try
    {
      $this->prdctMoveService->moveImage(1, null);
    }
    catch (\Exception $exception)
    {
      $this->assertEquals('Позиция не м.б. пустой.', $exception->getMessage(),
                          "Нельзя переместить изображение в никуда");
    }

    $expectedResult = [ 'request_img_id' => 1,'request_img_pos' => 0,'now_img_pos' => 3];

    $result = $this->prdctMoveService->moveImage($imageOne->getId(), $imageTwo->getPosition());
    $this->assertSame($expectedResult, $result, "Вернулись неверные данные из сервиса.");

    $this->em->clear();

    $imageOneDbData = $this->em->getRepository(ProductImage::class)->find($imageOne->getId());
    /** Проверим, что у первой картинки 3я позиция в БД */
    $this->assertSame(3, $imageOneDbData->getPosition());

    $this->em->clear();

    $imageTwoDbData = $this->em->getRepository(ProductImage::class)->find($imageTwo->getId());
    /** Проверим, что у 4ой картинки 0ая (т.е. 1ая) позиция в БД */
    $this->assertSame(0, $imageTwoDbData->getPosition());
  }
}