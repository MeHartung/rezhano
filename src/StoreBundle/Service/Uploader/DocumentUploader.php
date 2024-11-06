<?php

namespace StoreBundle\Service\Uploader;

use Symfony\Component\HttpFoundation\File\File;

class DocumentUploader
{
  private $documentStorage;

  public function __construct (DocumentStorage $documentStorage)
  {
    $this->documentStorage = $documentStorage;
  }

  public function upload(File $file, $name)
  {
    $dir = $this->documentStorage->getPath();
    $file->move($dir, $name);
  }
}