<?php

namespace Tests\DataFixtures\Document;


use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use StoreBundle\Entity\Document\UserDocument;

class UserDocumentFixture extends Fixture
{
  public function load (ObjectManager $manager)
  {
    $document = new UserDocument();
    $document
      ->setDocumentType($this->getReference('userDocumentType-passport'))
      ->setUuid('test-uuid')
      ->setName('Паспорт')
      ->setFile('passport.doc');

    $manager->persist($document);
    $manager->flush();
    $this->setReference('userDocument-passport', $document);
  }
}