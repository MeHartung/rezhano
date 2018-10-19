<?php

namespace StoreBundle\Twig;


use StoreBundle\Service\Uploader\DocumentStorage;

class DocumentExtension extends \Twig_Extension
{
  private $documentStorage;

  public function __construct (DocumentStorage $documentStorage)
  {
    $this->documentStorage = $documentStorage;
  }

  public function getFunctions ()
  {
    return [
      new \Twig_SimpleFunction('document_exists', [$this->documentStorage, 'isExists']),
      new \Twig_SimpleFunction('document_url', [$this->documentStorage, 'getUrl']),
      new \Twig_SimpleFunction('document_url_absolute', [$this->documentStorage, 'getAbsoluteUrl']),
      new \Twig_SimpleFunction('document_extension', [$this->documentStorage, 'getExtension']),
    ];
  }

}