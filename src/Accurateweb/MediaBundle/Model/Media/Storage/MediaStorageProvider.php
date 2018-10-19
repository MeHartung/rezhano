<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace Accurateweb\MediaBundle\Model\Media\Storage;


use Accurateweb\MediaBundle\Model\Media\MediaInterface;

class MediaStorageProvider
{
  private $storage;

  public function __construct(MediaStorageInterface $storage)
  {
    $this->galleryProviders = [];
    //File media storage is the only optino at the moment
    $this->storage = $storage;
  }

  public function getMediaStorage(MediaInterface $media=null)
  {
    return $this->storage;
  }
}