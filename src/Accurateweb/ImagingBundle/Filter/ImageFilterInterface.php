<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace Accurateweb\ImagingBundle\Filter;

use Accurateweb\ImagingBundle\Image\Image;

interface ImageFilterInterface
{
  public function process(Image $image);
}