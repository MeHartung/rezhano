<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace Accurateweb\ImagingBundle\Filter;

use Accurateweb\ImagingBundle\Image\Image;

interface FilterOptionsResolverInterface
{
  public function resolve(Image $image, array $options = array());
}