<?php

namespace Tests\DataFixtures\News;

use StoreBundle\Entity\Text\News;

class AllNewsNotPublishedFixtures extends \Doctrine\Bundle\FixturesBundle\Fixture
{

  public function load(\Doctrine\Common\Persistence\ObjectManager $manager)
  {
    $i = 0;

    while ($i < 6)
    {
      $news = new News();
      $news->setTitle('Новость ' . $i);
      $news->setText('Текст новости ' . $i);
      $news->setAnnounce('Анонс новости' .$i);
      $news->setPublished(false);
      $i++;

      $manager->persist($news);
      $manager->flush();
    }
  }


}