<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace Accurateweb\ImagingBundle\Resolver;


interface MimeTypeResolver
{
  public function resolve($filename);
}