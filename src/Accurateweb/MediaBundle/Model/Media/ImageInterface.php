<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace Accurateweb\MediaBundle\Model\Media;

use Accurateweb\MediaBundle\Model\Thumbnail\ImageThumbnail;

interface ImageInterface extends MediaInterface
{
  /**
   * Get thumbnail definitions
   *
   * @return mixed
   */
  public function getThumbnailDefinitions();

  /**
   * Get thumbnail
   *
   * @param $id Thumbnail id (as described in definition)
   * @return ImageThumbnail
   */
  public function getThumbnail($id);
}