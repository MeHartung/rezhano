<?php

namespace StoreBundle\Unit\Validator;

use StoreBundle\Entity\Store\Logistics\Warehouse\ProductStock;
use Tests\DataFixtures\Logistic\ProductStockFixture;
use Tests\DataFixtures\Logistic\WarehouseFixture;
use Tests\DataFixtures\Catalog\ProductFixture;
use Tests\DataFixtures\Logistic\CdekCityFixture;
use Tests\DataFixtures\Taxon\TaxonFixture;
use Tests\StoreBundle\StoreWebTestCase;

class OneWarehouseStockValidatorTest extends StoreWebTestCase
{
  protected function setUp ()
  {
    parent::setUp();
    $this->appendFixture(new TaxonFixture());
    $this->appendFixture(new ProductFixture());
    $this->appendFixture(new CdekCityFixture());
    $this->appendFixture(new WarehouseFixture());
    $this->appendFixture(new ProductStockFixture());
  }

  public function testStocksValidate()
  {
    $product = $this->getReference('product');
    $stock = new ProductStock();
    $stock
      ->setWarehouse($this->getReference('warehouse-msk'))
      ->setValue(5)
      ->setReservedValue(0)
      ->setProduct($product);

    $product->addStock($stock);
    $violations = $this->getClient()->getContainer()->get('validator')->validate($product);

    $this->assertCount(1, $violations, 'Ждали одну ошибку валидации');
    $violation = $violations[0];
    $this->assertSame('В соответствии с текущими правилами работы магазина товар может находиться только на одном складе. Пожалуйста, укажите остаток товара только на одном складе, и очистите данные о наличии на остальных складах.', $violation->getMessage());
  }
}