<?php
/**
 * Created by PhpStorm.
 * User: eobuh
 * Date: 31.07.2018
 * Time: 15:59
 */

namespace Tests\TaxonomyBundle\Unit;


use AccurateCommerce\Model\Taxonomy\FreeDeliveryTaxon;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\StoreBundle\ExcamWebTestCase;
use Tests\TaxonomyBundle\TaxonomyWebTestCase;

class TaxonomyResolverTest extends TaxonomyWebTestCase
{
  protected function setUp()
  {
    parent::setUp();
  }
  
  public function testResolver()
  {
    $manager = $this->client->getContainer()->get('aw.taxonomy.manager');
    $prdctRepository = $this->em->getRepository('StoreBundle:Store\Catalog\Product\Product');
  
    try # нет такого каталога
    {
      $manager->getTaxon('XAMARIN', $prdctRepository);
  
    }catch (NotFoundHttpException $exception)
    {
      $this->assertSame('Категория XAMARIN не найдена', $exception->getMessage(), 'Что-то не то в тексте 404 пришло');
    }
  
    # бесплатная доставка
    $taxon = $manager->getTaxon('besplatnaya-dostavka', $prdctRepository);
    $this->assertTrue($taxon instanceof  FreeDeliveryTaxon);
    $this->assertCount(0, $taxon->getChildren());
  }
  
}