<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace Accurateweb\ImagingBundle\Image;

class GdImage extends Image
{
  public function __construct($resource, $mimeType)
  {
    parent::__construct($resource, imagesx($resource), imagesy($resource), $mimeType);
  }

  public function __destruct()
  {
    if ($this->resource)
    {
      imagedestroy($this->resource);
    }
  }
}