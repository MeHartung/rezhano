<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace Accurateweb\MediaBundle\Model\Gallery;

use Accurateweb\MediaBundle\Model\Media\MediaInterface;

interface MediaGalleryInterface
{
  public function getId();

  /**
   * @return string
   */
  public function getName();

  /**
   * @return MediaObjectManager
   */
  public function getMediaObjectManager();

  /**
   * @param $id
   *
   * @return MediaInterface
   */
  public function createMedia();
}