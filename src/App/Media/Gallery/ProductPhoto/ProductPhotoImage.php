<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace App\Media\Gallery\ProductPhoto;

use Accurateweb\MediaBundle\Model\Image\Image;

class ProductPhotoImage extends Image
{
  public function getThumbnailDefinitions()
  {
    return array();
  }
}