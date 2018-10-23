<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 12.03.18
 * Time: 16:02
 */

namespace Tests\StoreBundle\Controller;


use StoreBundle\DataFixtures\News\AllNewsNotPublishedFixtures;
use StoreBundle\DataFixtures\News\NewsFixtures;
use Tests\StoreBundle\StoreWebTestCase;

class NewsControllerTest extends StoreWebTestCase
{
  public function setUp()
  {
    parent::setUp();
  }

  public function testEmptySideBarNews()
  {
    $crawler = $this->client->request('GET', '/');
    # на пустой таблице ничего не упало
    $this->assertSame(200, $this->client->getResponse()->getStatusCode());

    $sidebarDiv = $crawler->filter('.nspArtScroll2.nspPages1');
    // проверим, что не вывелось ни одной новости
    $this->assertSame(0, $sidebarDiv->children()->count());
  }

  /**
   * Новости есть, но ни одна не опубликована
   */
  public function allNewsNotPublished()
  {
    $this->appendFixture(new AllNewsNotPublishedFixtures(), true);

    $crawler = $this->client->request('GET', '/');
    $this->assertSame(200, $this->client->getResponse()->getStatusCode());

    $sidebarDiv = $crawler->filter('.nspArtScroll2.nspPages1');
    // проверим, что не вывелось ни одной новости
    $this->assertSame(0, $sidebarDiv->children()->count());
  }

  public function testSidebarNews()
  {
    $this->appendFixture(new NewsFixtures());

    $crawler = $this->client->request('GET', '/');
    $this->assertSame(200, $this->client->getResponse()->getStatusCode());

    $sidebarDiv = $crawler->filter('.nspArtScroll2.nspPages1');
    /** В сайдбаре должно быть 3 новости. */
    $this->assertSame(3, $sidebarDiv->children()->count());
  }

}