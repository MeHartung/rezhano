<?php
/**
 * @author Alexander Grinevich <agrinevich at accurateweb.ru>
 */

namespace Tests\StoreBundle\Controller;

use Tests\StoreBundle\ExcamWebTestCase;

class YandexMarketControllerTest extends ExcamWebTestCase
{
  /**
   * https://jira.accurateweb.ru/browse/EXCAM-229
   */
  public function testExportToYandexMarket()
  {
    $crawler = $this->client->request('GET', '/export/yandexmarket');
    $this->assertTrue($this->client->getResponse()->isOk());

    $this->assertSame(1, $crawler->filterXPath('//shop/offers/offer')->count(), 'В выгрузке должен быть один товар');
  }
}