<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace Accurateweb\MediaBundle\Model\Media\Storage;

use Accurateweb\MediaBundle\Model\Media\MediaInterface;
use Accurateweb\MediaBundle\Model\Media\Resource\MediaResourceInterface;
use Symfony\Component\HttpFoundation\File\File;

interface MediaStorageInterface
{
  public function store(MediaInterface $media, File $file);

  /**
   * Retrieve media from storage
   *
   * @param MediaInterface $media
   * @return MediaResourceInterface
   */
  public function retrieve(MediaInterface $media);

  /**
   * Returns true is a media file exists in the storage
   *
   * @param MediaInterface $media
   * @return bool
   */
  public function exists(MediaInterface $media);

  /**
   * Remove media from storage
   *
   * @param MediaInterface $media
   * @return mixed
   */
  public function remove(MediaInterface $media);
}