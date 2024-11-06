<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace Accurateweb\ImagingBundle\Adapter;


use Accurateweb\ImagingBundle\Exception\UnsupportedMimeTypeException;
use Accurateweb\ImagingBundle\Image\GdImage;
use Accurateweb\ImagingBundle\Image\Image;
use Accurateweb\ImagingBundle\Primitive\Size;
use Accurateweb\ImagingBundle\Resolver\ExtensionMimeTypeResolver;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class GdImageAdapter implements AdapterInterface
{
  /*
   * List of GD functions used to load specific image types
   * @var array
   */
  protected $loaders = array(
    'image/jpeg' => 'imagecreatefromjpeg',
    'image/jpg' => 'imagecreatefromjpeg',
    'image/gif' => 'imagecreatefromgif',
    'image/png' => 'imagecreatefrompng'
  );

  /*
   * List of GD functions used to create specific image types
   * @var array
   */
  protected $creators = array(
    'image/jpeg' => 'imagejpeg',
    'image/jpg' => 'imagejpeg',
    'image/gif' => 'imagegif',
    'image/png' => 'imagepng'
  );

  public function __construct()
  {
    // Check that the GD extension is installed and configured
    if (!extension_loaded('gd'))
    {
      throw new sfImageTransformException('The image processing library GD is not enabled. See PHP Manual for installation instructions.');
    }
  }

  /**
   * @param Size $size
   * @return GdImage
   */
  public function create(Size $size)
  {
    $resource = imagecreatetruecolor($size->getWidth(), $size->getHeight());

    return new GdImage($resource);
  }

  /**
   * @param $filename
   * @return GdImage|null
   */
  public function loadFromFile($filename)
  {
    if (!is_file($filename))
    {
      throw new FileNotFoundException(null, 0, null, $filename);
    }

    $resolver = new ExtensionMimeTypeResolver();

    $mimeType = $resolver->resolve($filename);

    if (array_key_exists($mimeType, $this->loaders))
    {
      $resource = $this->loaders[$mimeType]($filename);

      return new GdImage($resource, $mimeType);
    }

    return null;
  }

  /**
   * Save the image to disk
   *
   * @param string Filename
   * @param string MIME type
   * @return boolean
   */
  public function save(Image $image, $filename, $quality=100)
  {
    return $this->__output($image, $filename, $quality);
  }

  /**
   * Returns image in current format and optionally writes image to disk
   * @return resource
   *
   * @throws sfImageTransformException
   */
  protected function __output(Image $image, $filename, $quality=100)
  {
    $mime = $image->getMimeType();

    if (array_key_exists($mime, $this->creators))
    {
      switch ($mime)
      {

        case 'image/jpeg':
        case 'image/jpg':
          $output = $this->creators[$mime]($image->getResource(), $filename,$this->getImageSpecificQuality($quality, $mime));
          break;

        case 'image/png':
          imagesavealpha($image->getResource(), true);
          $output = $this->creators[$mime]($image->getResource(), $filename,$this->getImageSpecificQuality($quality, $mime), null);
          break;

        case 'image/gif':

          if (!is_null($filename))
          {
            $output = $this->creators[$mime]($image->getResource(), $filename);
          }
          else
          {
            $output = $this->creators[$mime]($image->getResource());
          }
          break;

        default:
        {
          throw new UnsupportedMimeTypeException(sprintf('Cannot convert as %s is an unsupported type' ,$mime));
        }

      }
    }
    else
    {
      throw new UnsupportedMimeTypeException(sprintf('Cannot convert as %s is an unsupported type' ,$mime));
    }

    return $output;
  }

  protected function getImageSpecificQuality($quality, $mime)
  {
    // Range is from 0-100

    if ('image/png' === $mime)
    {

      return 9 - round($quality * (9/100));
    }

    return $quality;
  }
}