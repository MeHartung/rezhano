<?php

namespace Tests\StoreBundle\Service\Geography;

use AccurateCommerce\GeoLocation\Geo;
use StoreBundle\Service\Geography\Location;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Tests\StoreBundle\ExcamWebTestCase;

/**
 * @see Location
 */
class LocationTest extends ExcamWebTestCase
{
  protected function setUp ()
  {
    parent::setUp();
  }

  public function testDetect()
  {
    $this->overrideGeo();
    $location_service = $this->client->getContainer()->get('store.geography.location');

    $this->assertSame('Москва', $location_service->getCityName());
  }

    /**
     * Не работает phpunit с сервисами из вне
     * curl_exec всегда вернёт null
     */
/*  public function testGetCityNameByPostCode()
  {
    $this->overrideGeo();
    $location_service = $this->client->getContainer()->get('store.geography.location');
    $city = $location_service->getCityNameByPostcode('620000');
    $this->assertSame('Екатеринбург', $city);
  }*/

  protected function overrideGeo($ip = '89.113.224.55')
  {
    $request = new Request(
      [],
      [],
      [],
      [/*cookie*/],
      [],
      ['REMOTE_ADDR' => $ip]
    );
    $request_stack = new RequestStack();
    $request_stack->push($request);

    $geo = new Geo($request_stack);
    $this->client->getContainer()->set('store.geography.geo', $geo);
  }
}