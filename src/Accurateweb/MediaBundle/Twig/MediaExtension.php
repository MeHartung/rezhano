<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace Accurateweb\MediaBundle\Twig;


use Accurateweb\MediaBundle\Model\Image\Image;
use Accurateweb\MediaBundle\Model\Image\ImageAwareInterface;
use Accurateweb\MediaBundle\Model\Media\ImageInterface;
use Accurateweb\MediaBundle\Model\Media\Resource\MediaResource;
use Accurateweb\MediaBundle\Model\Media\Resource\WebResourceFactory;
use Accurateweb\MediaBundle\Model\Media\Storage\MediaStorageInterface;
use StoreBundle\Entity\Store\Catalog\Product\Product;

class MediaExtension extends \Twig_Extension
{
  private $mediaStorage;

  public  function __construct(MediaStorageInterface $storage)
  {
    $this->mediaStorage = $storage;
  }

  public function getFunctions()
  {
    return array(
      new \Twig_SimpleFunction('image_thumbnail_url', array($this, 'getImageThumbnailUrl')),
      new \Twig_SimpleFunction('image_url', array($this, 'getImageUrl')),
      new \Twig_SimpleFunction('image_exists', array($this, 'imageExists')),
      new \Twig_SimpleFunction('image_width', array($this, 'getImageWidth')),
      new \Twig_SimpleFunction('image_height', array($this, 'getImageHeight')),
    );
  }

  /**
   * Выводит миниатюру изображения
   */
  public function getImageThumbnailUrl(ImageAwareInterface $imageAware, $imageId, $thumbnailId)
  {
    $image = $imageAware->getImage($imageId);

    $thumbnail = $image->getThumbnail($thumbnailId);

    $mediaResource = null;
    if ($thumbnail)
    {
      $mediaResource = $this->mediaStorage->retrieve($thumbnail);
    }

    if (!$mediaResource)
    {
      return null;
    }

    return $mediaResource->getUrl();
  }

  public function getImageUrl($imageAware, $imageId=null)
  {
    if(!$imageAware instanceof ImageAwareInterface)
    {
      return null;
    }

    $image = $imageAware->getImage($imageId);
    $mediaResource = null;

    if ($image)
    {
      $mediaResource = $this->mediaStorage->retrieve($image);
    }

    if (!$mediaResource)
    {
      return null;
    }

    return $mediaResource->getUrl();
  }

  public function imageExists(ImageAwareInterface $imageAware, $imageId=null)
  {
    $image = $imageAware->getImage($imageId);

    return $image && $this->mediaStorage->exists($image);
  }

  public function getImageWidth(ImageAwareInterface $imageAware, $imageId=null)
  {
    $imageSize = $this->getImageSize($imageAware, $imageId);
    if (false !== $imageSize)
    {
      return $imageSize[0];
    }

    return null;
  }

  public function getImageHeight(ImageAwareInterface $imageAware, $imageId=null)
  {
    $imageSize = $this->getImageSize($imageAware, $imageId);
    if (false !== $imageSize)
    {
      return $imageSize[1];
    }

    return null;
  }

  protected function getImageSize(ImageAwareInterface $imageAware, $imageId=null)
  {
    if(!$imageAware instanceof ImageAwareInterface)
    {
      return null;
    }

    $image = $imageAware->getImage($imageId);

    if ($image)
    {
      $mediaResource = $this->mediaStorage->retrieve($image);
    }

    if (!$mediaResource)
    {
      return false;
    }

    return getimagesize($mediaResource->getPath());
  }
}