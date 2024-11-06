<?php

namespace Tests\StoreBundle\Unit\Form\Document;

use StoreBundle\Entity\Document\UserDocument;
use StoreBundle\Service\Uploader\DocumentStorage;
use Tests\DataFixtures\Document\UserDocumentFixture;
use Tests\DataFixtures\Document\UserDocumentTypeFixture;
use Tests\StoreBundle\Unit\Form\FormStoreWebTestCase;

class EntrepreneurUserDocumentFormTypeTest extends FormStoreWebTestCase
{
  public function testSubmit()
  {
    $documentStorage = new DocumentStorage($this->getResourceDir(), '', $this->getClient()->getContainer()->get('router'));
    $this->getClient()->getContainer()->set('store.document.storage', $documentStorage);
    $this->appendFixture(new UserDocumentTypeFixture());
    $this->appendFixture(new UserDocumentFixture());
    $form = $this->factory->create('StoreBundle\Form\Document\EntrepreneurUserDocumentFormType', null, [
      'csrf_protection' => false,
    ]);
    /** @var UserDocument $passport */
    $passport = $this->getReference('userDocument-passport');

    $data = [
      [
        'file' => $this->getClient()->getContainer()->get('store.document.storage')->getFile($passport),
        'name' => $passport->getName(),
        'documentType' => $passport->getDocumentType()->getId(),
      ]
    ];

    $form->submit($data);
    $this->assertTrue($form->isValid());
  }
}