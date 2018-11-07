<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Media\Text;

use Accurateweb\ImagingBundle\Filter\CropFilterOptionsResolver;
use Accurateweb\ImagingBundle\Filter\FilterChain;
use Accurateweb\MediaBundle\Model\Image\Image;
use Accurateweb\MediaBundle\Model\Thumbnail\ThumbnailDefinition;

class PartnerImage extends Image
{
  private $filterOptions;

  public function __construct($id, $resourceId, $options)
  {
    $this->filterOptions = $options;

    parent::__construct('partner/'.$id, $resourceId);
  }

  public function getThumbnailDefinitions()
  {
    return array(
      new ThumbnailDefinition('view', new FilterChain(array(
        array(
         'id' => 'resize', 'options' => array('size' => '310x81'),
        )
      )))
    );
  }

  protected function getFilterOptions($id, $default=array())
  {
    return isset($this->filterOptions[$id]) ? $this->filterOptions[$id] : $default;
  }
}