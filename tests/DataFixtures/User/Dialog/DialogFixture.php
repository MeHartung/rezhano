<?php

namespace Tests\DataFixtures\User\Dialog;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use StoreBundle\Entity\Text\Dialog\Dialog;
use StoreBundle\Entity\Text\Dialog\DialogMessage;

class DialogFixture extends Fixture
{
  public function load (ObjectManager $manager)
  {
    $dialog = new Dialog();
    $dialog
      ->setCreator($this->getReference('user-admin'))
      ->setDialogType(Dialog::DIALOG_TYPE_QUESTION);
    $message = new DialogMessage();
    $message
      ->setUser($this->getReference('user-admin'))
      ->setUserName('Test')
      ->setUserEmail('Test@example.com')
      ->setMessage('npuBet');
    $dialog->addMessage($message);

    $dialogNotReaded = new Dialog();
    $dialogNotReaded
      ->setCreator($this->getReference('user-admin'))
      ->setDialogType(Dialog::DIALOG_TYPE_QUESTION);
    $message = new DialogMessage();
    $message
      ->setUser($this->getReference('user-admin'))
      ->setUserName('Test')
      ->setUserEmail('Test@example.com')
      ->setMessage('Very Ba}|{Ho');
    $dialogNotReaded->addMessage($message);


    $manager->persist($dialog);
    $manager->persist($dialogNotReaded);
    $manager->flush();
    $this->setReference('dialog', $dialog);
    $this->setReference('dialog-notReaded', $dialogNotReaded);
  }

}