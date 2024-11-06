<?php

namespace Tests\DataFixtures\Document;


use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use StoreBundle\Entity\Document\UserDocumentType;

class UserDocumentTypeFixture extends Fixture
{
  public function load (ObjectManager $manager)
  {
    $type = new UserDocumentType();
    $type
      ->setName('Паспорт')
      ->setFile('document.txt')
      ->setShowJuridical(true)
      ->setShowEnterpreneur(true)
      ->setShowIndividual(true);
    $manager->persist($type);
    $manager->flush();

    $this->setReference('userDocumentType-passport', $type);
  }

}