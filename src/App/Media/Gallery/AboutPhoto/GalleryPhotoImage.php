<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace App\Media\Gallery\AboutPhoto;

use Accurateweb\MediaBundle\Model\Image\Image;

class GalleryPhotoImage extends Image
{
  public function getThumbnailDefinitions()
  {
    return array();
  }
}