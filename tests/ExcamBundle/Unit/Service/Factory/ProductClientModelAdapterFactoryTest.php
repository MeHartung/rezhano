<?php

namespace Tests\StoreBundle\Unit\Service\Factory;

use Tests\StoreBundle\StoreWebTestCase;

class ProductClientModelAdapterFactoryTest extends StoreWebTestCase
{
  /**
   * @dataProvider dataRolesPrices
   */
  public function testGetModelAdapter($user, $price)
  {
    $factory = $this->client->getContainer()->get('store.factory.product_client_adapter');

    if ($user)
    {
      $this->logIn($this->getByReference($user));
    }

    $product = $this->getByReference('product-role');
    $model_adapter = $factory->getModelAdapter($product);
    $values = $model_adapter->getClientModelValues();

    $this->assertEquals($price, $values['price']);
    $this->assertSame('gopro-suction-cup', $values['slug']);
    $this->assertSame('Присоска GoPro Suction Cup', $values['name']);
  }

  public function dataRolesPrices()
  {
    return [
      'anon.'     => [null, 5000],
      'club'      => ['user-customer', 4000],
      'wholesale' => ['user-wholesale', 3000]
    ];
  }
}