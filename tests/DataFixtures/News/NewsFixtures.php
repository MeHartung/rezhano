<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 07.02.18
 * Time: 18:49
 */

namespace Tests\DataFixtures\News;


use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use StoreBundle\Entity\Text\News;

class NewsFixtures extends Fixture
{
  public function load (ObjectManager $manager)
  {
    $i = 1;

    while ($i < 6)
    {
      $news = new News();
      $news->setTitle('Новость ' . $i);
      $news->setText('Текст новости ' . $i);
      $news->setAnnounce('Анонс новости' .$i);
      $news->setPublished(true);
      $i++;

      $manager->persist($news);
      $manager->flush();
    }

    while ($i < 10)
    {
      $news = new News();
      $news->setTitle('Новость ' . $i);
      $news->setText('Текст новости ' . $i);
      $news->setAnnounce('Анонс новости' .$i);
      $news->setPublished(true);
      $i++;

      $manager->persist($news);
      $manager->flush();
    }

  }
}