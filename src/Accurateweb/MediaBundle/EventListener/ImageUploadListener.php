<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace Accurateweb\MediaBundle\EventListener;


use Accurateweb\MediaBundle\Model\Image\ImageAwareInterface;
use Accurateweb\MediaBundle\Model\Media\ImageInterface;
use Accurateweb\MediaBundle\Service\ImageUploader;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Gedmo\Mapping\MappedEventSubscriber;
use StoreBundle\Media\Text\NewsImage;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ImageUploadListener
{
  private $uploader;

  private $annotationReader;

  private $propertyAccessor;

  public function __construct(ImageUploader $uploader, AnnotationReader $annotationReader)
  {
    $this->uploader = $uploader;
    $this->annotationReader = $annotationReader;
    $this->propertyAccessor = PropertyAccess::createPropertyAccessor();

  }

  public function prePersist(LifecycleEventArgs $args)
  {
    $entity = $args->getEntity();

    $this->uploadFile($entity, $args->getObjectManager());
  }

  public function preUpdate(PreUpdateEventArgs $args)
  {
    $entity = $args->getEntity();

    $this->uploadFile($entity, $args->getObjectManager());
  }

  /**
   *
   * @param $object
   * @param ObjectManager $om
   */
  private function uploadFile($object, $om)
  {
    if (!$object instanceof ImageAwareInterface)
    {
      return;
    }

    $meta = $om->getClassMetadata(get_class($object));
    $rc = $meta->getReflectionClass();
    $props = $rc->getProperties();

    foreach ($props as $prop)
    {
      $ann = $this->annotationReader->getPropertyAnnotation($prop, '\\Accurateweb\\MediaBundle\\Annotation\\Image');
      if ($ann)
      {
        $file = $meta->getReflectionProperty($prop->name)->getValue($object);

        $image = $this->propertyAccessor->getValue($object, $prop->name.'_image');

        if (!$image instanceof ImageInterface)
        {
          continue;
        }

        // only upload new files
        if (!$file instanceof UploadedFile)
        {
          continue;
        }

        $resourceId = implode('/', [
          $image->getId(),
          md5(uniqid()) . ($file->guessExtension() ? '.' . $file->guessExtension() : '')
        ]);

        $image->setResourceId($resourceId);

        $this->uploader->upload($image, $file);

        $this->propertyAccessor->setValue($object, $prop->name.'_image', $image);
      }
    }
  }

}