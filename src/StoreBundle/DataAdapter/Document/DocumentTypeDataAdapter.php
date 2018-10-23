<?php

namespace AppBundle\DataAdapter\Document;

use Accurateweb\ClientApplicationBundle\DataAdapter\ClientApplicationModelAdapterInterface;
use StoreBundle\Entity\Document\UserDocumentType;
use StoreBundle\Service\Uploader\DocumentStorage;

class DocumentTypeDataAdapter implements ClientApplicationModelAdapterInterface
{
  private $documentStorage;

  public function __construct (DocumentStorage $documentStorage)
  {
    $this->documentStorage = $documentStorage;
  }

  /**
   * @param $subject UserDocumentType
   * @param array $options
   * @return array
   */
  public function transform ($subject, $options = array())
  {
    return [
      'id' => $subject->getId(),
      'name' => $subject->getName(),
      'blank' => $this->documentStorage->isExists($subject)?$this->documentStorage->getUrl($subject):null,
    ];
  }

  public function getModelName ()
  {
    return 'UserDocumentType';
  }

  public function supports ($subject)
  {
    return $subject instanceof UserDocumentType;
  }

}