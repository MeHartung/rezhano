<?php

namespace Accurateweb\ImagingBundle\Crop;

use Accurateweb\ImagingBundle\Image\Image;
use Accurateweb\ImagingBundle\Primitive\Point;
use Accurateweb\ImagingBundle\Primitive\Rectangle;
use Accurateweb\ImagingBundle\Primitive\Size;

class AutoCrop extends Crop
{

  public function __construct(Image $image, $type, $aspectRatio)
  {
    if ((double) $aspectRatio == 0)
    {
      throw new InvalidArgumentException('Aspect ratio must be a nonzero floating point value. "%s" given', $aspectRatio);
    }

    if (!in_array($type, array('left-top', 'center-top', 'right-top', 'left-middle', 'right-middle', 'left-bottom', 'center-bottom', 'right-bottom', 'center-middle', 'center')))
    {
      throw new InvalidArgumentException('Autocrop type "%s" not supported', $type);
    }

    parent::__construct();

    $this->fit(new Size($image->getWidth(), $image->getHeight()), $type, $aspectRatio);
  }

  private function fit($size, $type, $aspectRatio)
  {
    $fit = null;
    switch ($type)
    {
      case 'left-top': $fit = $this->fitLeftTop($size, $aspectRatio); break;
      case 'center-top': $fit = $this->fitCenterTop($size, $aspectRatio); break;
      case 'right-top':  $fit = $this->fitRightTop($size, $aspectRatio); break;
      case 'left-middle': $fit = $this->fitLeftMiddle($size, $aspectRatio); break;
      case 'center-middle':
      case 'center':       $fit = $this->fitCenterMiddle($size, $aspectRatio);  break;        
      case 'right-middle': $fit = $this->fitRightMiddle($size, $aspectRatio); break;
      case 'left-bottom': $fit = $this->fitLeftBottom($size, $aspectRatio);  break;
      case 'center-bottom': $fit = $this->fitCenterBottom($size, $aspectRatio); break;
      case 'right-bottom': $fit = $this->fitRightBottom($size, $aspectRatio); break;
      default: $fit = $this->fitCenterMiddle($size, $aspectRatio);
    }
    
    if ($fit)
    {
      $this->setLocation($fit->getLocation());
      $this->setSize($fit->getSize());
    }
  }

  private function fitCenterMiddle($size, $aspectRatio)
  {
    $h = $size->getWidth() / $aspectRatio;

    if ($h <= $size->getHeight())
    {
      $cropSize = new Size($size->getWidth(), $h);
      $cropLocation = new Point(0, ( $size->getHeight() - $h ) / 2);

      return new Rectangle($cropLocation, $cropSize);
    }
    else
    {
      $w = $size->getHeight() * $aspectRatio;

      if ($w <= $size->getWidth())
      {
        $cropSize = new Size($w, $size->getHeight());
        $cropLocation = new Point(($size->getWidth() - $w) / 2, 0);
        return new Rectangle($cropLocation, $cropSize);
      }
      else
      {
        return null;
      }
    }
  }

  private function fitLeftTop($size, $aspectRatio)
  {
    $cropSize = $this->getMaxCropSize($size, $aspectRatio);
    return $cropSize ? new Rectangle(new Point(0, 0), $cropSize) : null;
  }

  private function fitCenterTop($size, $aspectRatio)
  {
    $cropSize = $this->getMaxCropSize($size, $aspectRatio);
    return $cropSize ? new Rectangle(new Point(($size->getWidth() - $cropSize->getWidth()) / 2, 0), $cropSize) : null;
  }

  function fitRightTop($size, $aspectRatio)
  {
    $cropSize = $this->getMaxCropSize($size, $aspectRatio);
    return $cropSize ? new Rectangle(new Point($size->getWidth() - $cropSize->getWidth(), 0), $cropSize) : null;
  }

  private function fitLeftMiddle($size, $aspectRatio)
  {
    $cropSize = $this->getMaxCropSize($size, $aspectRatio);

    return $cropSize ? new Rectangle(new Point(0, ($size->getHeight() - $cropSize->getHeight()) / 2), $cropSize) : null;
  }

  private function fitRightMiddle($size, $aspectRatio)
  {
    $cropSize = $this->getMaxCropSize($size, $aspectRatio);


    return ($cropSize ? new Rectangle(new Point($size->getWidth() - $cropSize->getWidth(), ( $size->getHeight() - $cropSize->getHeight() ) / 2), $cropSize) : null);
  }

  private function fitLeftBottom($size, $aspectRatio)
  {
    $cropSize = $this->getMaxCropSize($size, $aspectRatio);

    return $cropSize ? new Rectangle(new Point(0, $size->getHeight() - $cropSize->getHeight()), $cropSize) : null;
  }

  private function fitCenterBottom($size, $aspectRatio)
  {
    $cropSize = $this->getMaxCropSize($size, $aspectRatio);

    return $cropSize ? new Rectangle(new Point(( $size->getWidth() - $cropSize->getWidth() ) / 2, $size->getHeight() - $cropSize->getHeight()), $cropSize) : null;
  }

  function fitRightBottom($size, $aspectRatio)
  {
    $cropSize = $this->getMaxCropSize($size, $aspectRatio);

    return $cropSize ? new Rectangle(new Point($size->getWidth() - $cropSize->getWidth(), $size->getHeight() - $cropSize->getHeight()), $cropSize) : null;
  }

  private function getMaxCropSize($size, $aspectRatio)
  {
    $cropSize = null;
    $h = $size->getWidth() / $aspectRatio;

    if ($h <= $size->getHeight())
    {
      $cropSize = new Size($size->getWidth(), $h);
    }
    else
    {
      $w = $size->getHeight() * $aspectRatio;

      if ($w <= $size->getWidth())
      {
        $cropSize = new Size($w, $size->getHeight());
      }
    }


    return $cropSize;
  }

}
