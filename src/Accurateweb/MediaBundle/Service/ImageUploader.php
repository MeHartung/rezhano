<?php

namespace Accurateweb\MediaBundle\Service;

use Accurateweb\ImagingBundle\Adapter\GdImageAdapter;
use Accurateweb\ImagingBundle\Filter\GdFilterFactory;
use Accurateweb\MediaBundle\Generator\ImageThumbnailGenerator;
use Accurateweb\MediaBundle\Model\Media\ImageInterface;
use Accurateweb\MediaBundle\Model\Media\MediaInterface;
use Accurateweb\MediaBundle\Model\Media\MediaManager;
use Accurateweb\MediaBundle\Model\Media\Storage\MediaStorageProvider;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */
class ImageUploader
{
  private $mediaStorageProvider;

  public function __construct(MediaStorageProvider $mediaStorageProvider)
  {
    $this->mediaStorageProvider = $mediaStorageProvider;
  }

  public function upload(ImageInterface $image, UploadedFile $file)
  {
    $storage = $this->mediaStorageProvider->getMediaStorage($image);

    $storage->store($image, $file);

    $generator = new ImageThumbnailGenerator($this->mediaStorageProvider->getMediaStorage($image),
      new GdImageAdapter(), new GdFilterFactory());

    $generator->generate($image);
  }
}