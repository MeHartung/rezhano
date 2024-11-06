<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace Accurateweb\ImagingBundle\Filter\GD;


use Accurateweb\ImagingBundle\Filter\ImageFilterInterface;
use Accurateweb\ImagingBundle\Image\Image;

abstract class GdFilter implements ImageFilterInterface
{
  protected function createTransparentImage(Image $image, $width, $height)
  {
    $resource = $image->getResource();

    $dest_resource = imagecreatetruecolor((int)$width, (int)$height);

    // Preserve alpha transparency
    if (in_array($image->getMimeType(), array('image/gif','image/png')))
    {
      $index = imagecolortransparent($resource);

      // Handle transparency
      if ($index >= 0)
      {

        // Grab the current images transparent color
        $index_color = imagecolorsforindex($resource, $index);

        // Set the transparent color for the resized version of the image
        $index = imagecolorallocate($dest_resource, $index_color['red'], $index_color['green'], $index_color['blue']);

        // Fill the entire image with our transparent color
        imagefill($dest_resource, 0, 0, $index);

        // Set the filled background color to be transparent
        imagecolortransparent($dest_resource, $index);
      }
      else if ($image->getMimeType() == 'image/png') // Always make a transparent background color for PNGs that don't have one allocated already
      {

        // Disabled blending
        imagealphablending($dest_resource, false);

        // Grab our alpha tranparency color
        $color = imagecolorallocatealpha($dest_resource, 0, 0, 0, 127);

        // Fill the entire image with our transparent color
        imagefill($dest_resource, 0, 0, $color);

        // Re-enable transparency blending
        imagesavealpha($dest_resource, true);
      }
    }

    return $dest_resource;
  }
}