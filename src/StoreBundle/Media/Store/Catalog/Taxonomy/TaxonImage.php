<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Media\Store\Catalog\Taxonomy;

use Accurateweb\ImagingBundle\Filter\FilterChain;
use Accurateweb\MediaBundle\Model\Image\Image;
use Accurateweb\MediaBundle\Model\Thumbnail\ThumbnailDefinition;

class TaxonImage extends Image
{

  public function getThumbnailDefinitions()
  {
    return array(
      new ThumbnailDefinition('150x113', new FilterChain(array(
        array('id' => 'resize', 'options' => array('size' => '150x113'))
      )))
    );
  }

  public function getThumbnail($id)
  {

  }


}