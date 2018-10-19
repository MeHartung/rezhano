<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace Accurateweb\ImagingBundle\Resolver;


class ExtensionMimeTypeResolver implements MimeTypeResolver
{
  /*
   * MIME type map and their associated file extension(s)
   * @var array
   */
  protected $types = array(
    'image/gif' => array('gif'),
    'image/jpeg' => array('jpg', 'jpeg'),
    'image/png' => array('png'),
    'image/svg' => array('svg'),
    'image/tiff' => array('tiff')
  );

  public function resolve($filename)
  {
    $pathinfo = pathinfo($filename);

    foreach($this->types as $mime => $extension)
    {
      if (in_array(strtolower($pathinfo['extension']), $extension))
      {
        return $mime;
      }
    }

    return false;
  }
}