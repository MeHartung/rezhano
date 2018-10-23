<?php

namespace Tests\StoreBundle\Service\DataExport;

use AccurateCommerce\Shipping\Method\App\ShippingMethodExcamFree;
use AccurateCommerce\Shipping\Shipment\Address;
use AccurateCommerce\Shipping\Shipment\Shipment;
use StoreBundle\DataExport\YandexMarketXmlBuilder;
use StoreBundle\Entity\Store\Order\Order;
use Tests\StoreBundle\ExcamWebTestCase;

class YandexMarketXmlBuilderTest extends ExcamWebTestCase
{
  /** @var YandexMarketXmlBuilder */
  private $yandex_builder;

  protected function setUp ()
  {
    parent::setUp();
    $this->yandex_builder = $this->client->getContainer()->get('store.data_export.yandexmarket');
  }


  public function testBuild()
  {
    try
    {
      $xml = $this->yandex_builder->build();
    }
    catch (\Exception $e)
    {
      $this->fail('Экспорт в маркет сломался');
    }

    $document = new \DOMDocument();
    $is_load = $document->loadXML($xml);
    $this->assertTrue($is_load, 'Что-то сгенерировалось, но не xml');

    $is_valid = $document->schemaValidate(__DIR__.'/shops_with_byn.xsd');
    $this->assertTrue($is_valid, 'Сгенерировался xml, но не валидный');
  }
}