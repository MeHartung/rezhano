<?php

namespace StoreBundle\EventListener\Upload;

use Doctrine\ORM\Event\LifecycleEventArgs;
use StoreBundle\Entity\Document\Document;
use StoreBundle\Service\Uploader\DocumentUploader;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class DocumentUploadListener
{
  private $uploader;

  public function __construct(DocumentUploader $uploader)
  {
    $this->uploader = $uploader;
  }

  public function prePersist(LifecycleEventArgs $args)
  {
    $entity = $args->getEntity();
    $this->uploadFile($entity);
  }

  public function preUpdate(LifecycleEventArgs $args)
  {
    $entity = $args->getEntity();
    $this->uploadFile($entity);
  }

  private function uploadFile($entity)
  {
    if (!$entity instanceof Document) {
      return;
    }

    $file = $entity->getFile();

    if (!$file instanceof File)
    {
      return;
    }

    if ($file instanceof UploadedFile)
    {
      $ext = $file->getClientOriginalExtension() ? $file->getClientOriginalExtension() : $file->guessExtension();
    }
    else
    {
      $ext = $file->getExtension();
    }

    $resourceId = sprintf('%s.%s', md5(uniqid()),$ext);
    $entity->setFile($resourceId);
    $this->uploader->upload($file, $resourceId);
  }
}