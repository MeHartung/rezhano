<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace Accurateweb\MediaBundle\Model\Media;


use Accurateweb\MediaBundle\Model\Gallery\MediaGalleryProviderInterface;
use Accurateweb\MediaBundle\Model\Media\Storage\FileMediaStorage;
use Accurateweb\MediaBundle\Model\Media\Storage\MediaStorageInterface;
use Accurateweb\MediaBundle\Model\Media\Storage\MediaStorageProvider;
use Accurateweb\MediaBundle\Model\MediaGallery\MediaGalleryInterface;
use Psr\Log\InvalidArgumentException;

class MediaManager
{
  private $galleryProviders;

  private $mediaStorageProvider;

  public function __construct(MediaStorageProvider $mediaStorageProvider)
  {
    $this->mediaStorageProvider = $mediaStorageProvider;
  }

  /**
   * @param MediaGalleryProviderInterface $provider
   * @param $alias
   */
  public function addGalleryProvider(MediaGalleryProviderInterface $provider, $alias)
  {
    $this->galleryProviders[$alias] = $provider;
  }

  /**
   * @param $entity
   * @param $name
   *
   * @return \Accurateweb\MediaBundle\Model\Gallery\MediaGalleryInterface
   */
  public function getGallery($providerId, $galleryId)
  {
    if (!isset($this->galleryProviders[$providerId]))
    {
      throw new InvalidArgumentException(sprintf('Provider "%d" is not registered', $providerId));
    }

    return $this->galleryProviders[$providerId]->provide($galleryId);
  }

  /**
   * Returns media storage for given media.
   *
   * @param MediaInterface $media
   *
   * @return MediaStorageInterface
   */
  public function getMediaStorage(MediaInterface $media=null)
  {
    return $this->mediaStorageProvider->getMediaStorage($media);
  }
}