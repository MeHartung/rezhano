<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 16.01.18
 * Time: 10:25
 */

namespace Tests\StoreBundle\Unit\Entity;

use StoreBundle\Entity\Store\Catalog\Product\Product;
use Tests\StoreBundle\Controller\preparationForTest;
use Tests\StoreBundle\StoreWebTestCase;

class ProductTest extends StoreWebTestCase
{
  /**
   * Проверям, становится ли созданный товар новинкой
   */
  public function testIsNovice()
  {
    $product = new Product();

    $product->setPrice(100);
    $product->setDescription('Desc');
    $product->setName('Good Name');
    $product->setShortDescription('Short Desc');
    $product->setSku('good-news');

    $this->getEntityManager()->persist($product);
    $this->getEntityManager()->flush();

    $this->getEntityManager()->refresh($product);
    /** Проверяем, что продукт после создания стал новинкой */
    $this->assertTrue($product->isNovice());
  }

  public function testCloneProduct()
  {
    /** @var Product $product */
    $product = $this->getByReference('product-go-pro');
    $clone = clone $product;

    $this->getEntityManager()->persist($clone);
    $this->getEntityManager()->flush($clone);

    $this->assertSame($product->getPrice(), $clone->getPrice());
    $this->assertSame($product->getName().' копия', $clone->getName());
    $this->assertSame($this->count($product->getProductAttributeValuesToProducts()), $this->count($clone->getProductAttributeValuesToProducts()));
    $slug = $this->client->getContainer()->get('accurateweb.slugifier.yandex')->slugify($clone->getName());
    $this->assertSame($slug, $clone->getSlug());
  }
}