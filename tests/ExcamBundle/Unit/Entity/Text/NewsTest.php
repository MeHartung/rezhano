<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 16.01.18
 * Time: 12:23
 */

namespace StoreBundle\Unit\Entity\Text;


use Doctrine\ORM\EntityManagerInterface;
use StoreBundle\DataFixtures\News\NewsFixtures;
use StoreBundle\Entity\Text\News;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\StoreBundle\StoreWebTestCase;

class NewsTest extends StoreWebTestCase
{
  public function setUp()
  {
    parent::setUp();
    $this->appendFixture(new NewsFixtures());
  }

  /**
   * Этот метод возращает  новости для сайдбара
   * https://jira.accurateweb.ru/browse/EXCAM-180
   */
  public function testFindRecent()
  {
    $recentNews = $this->em->getRepository('StoreBundle:Text\News')->findRecent();

    /** Проверим, что метод вернул 3 новости */
    $this->assertEquals(3, count($recentNews));

    /**
     * @var $news News
     */
    foreach ($recentNews as $news)
    {/** Проверим, что каждая из них опубликована */
      $this->assertTrue($news->isPublished());
    }

  }
}