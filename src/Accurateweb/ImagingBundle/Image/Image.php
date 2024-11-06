<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace Accurateweb\ImagingBundle\Image;


use Accurateweb\ImagingBundle\Primitive\Size;

abstract class Image
{
  protected $resource;

  protected $width;

  protected $height;

  protected $mimeType;

  public function __construct($resource, $width, $height, $mimeType)
  {
    $this->resource = $resource;
    $this->width = $width;
    $this->height = $height;
    $this->mimeType = $mimeType;
  }

  public function getResource()
  {
    return $this->resource;
  }

  public function setResource($resource)
  {
    $this->resource = $resource;
  }

  /**
   * @return mixed
   */
  public function getWidth()
  {
    return $this->width;
  }

  /**
   * @return mixed
   */
  public function getHeight()
  {
    return $this->height;
  }

  public function getSize()
  {
    return new Size($this->width, $this->height);
  }

  /**
   * @return mixed
   */
  public function getMimeType()
  {
    return $this->mimeType;
  }


}