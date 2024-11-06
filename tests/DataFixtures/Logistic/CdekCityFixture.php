<?php

namespace Tests\DataFixtures\Logistic;


use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use StoreBundle\Entity\Store\Logistics\Delivery\Cdek\CdekCity;

class CdekCityFixture extends Fixture
{
  public function load (ObjectManager $manager)
  {
    $moscow = new CdekCity();
    $moscow->setName('Москва')
      ->setCode(44)
      ->setRegion('Московская обл.');

    $ekb = new CdekCity();
    $ekb->setName('Екатеринбург')
      ->setCode(25)
      ->setRegion('Свердловская обл.');

    $spb = new CdekCity();
    $spb->setName('Санкт-петербург');

    $nn = new CdekCity();
    $nn->setName('Нижний новгород');

    $manager->persist($moscow);
    $manager->persist($ekb);
    $manager->persist($spb);
    $manager->persist($nn);
    $manager->flush();

    $this->addReference('city-msk', $moscow);
    $this->addReference('city-ekb', $ekb);
    $this->addReference('city-spb', $spb);
    $this->addReference('city-nn', $nn);
  }
}