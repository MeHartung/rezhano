<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace Accurateweb\MediaBundle\EventListener;


use Accurateweb\MediaBundle\Model\Image\ImageAwareInterface;
use Accurateweb\MediaBundle\Model\Media\ImageInterface;
use Accurateweb\MediaBundle\Service\ImageUploader;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use StoreBundle\Media\Text\NewsImage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageUploadListener
{
  private $uploader;

  public function __construct(ImageUploader $uploader)
  {
    $this->uploader = $uploader;
  }

  public function prePersist(LifecycleEventArgs $args)
  {
    $entity = $args->getEntity();

    $this->uploadFile($entity);
  }

  public function preUpdate(PreUpdateEventArgs $args)
  {
    $entity = $args->getEntity();

    $this->uploadFile($entity);
  }

  private function uploadFile($entity)
  {
    if (!$entity instanceof ImageAwareInterface) {
      return;
    }

    $image = $entity->getImage();

    if (!$image instanceof ImageInterface)
    {
      return;
    }

    $file = $image->getResourceId();

    // only upload new files
    if (!$file instanceof UploadedFile)
    {
      return;
    }

    $resourceId = implode('/', [
      $image->getId(),
      md5(uniqid()).($file->guessExtension() ? '.'.$file->guessExtension() : '')
    ]);

    $image->setResourceId($resourceId);

    $this->uploader->upload($image, $file);

    $entity->setImage($image);
  }
}