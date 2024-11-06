<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace Accurateweb\ImagingBundle\Adapter;


interface AdapterInterface
{
  public function loadFromFile($filename);
}