<?php

namespace AppBundle\Media\Text;


use Accurateweb\ImagingBundle\Filter\CropFilterOptionsResolver;
use Accurateweb\ImagingBundle\Filter\FilterChain;
use Accurateweb\MediaBundle\Model\Image\Image;
use Accurateweb\MediaBundle\Model\Thumbnail\ThumbnailDefinition;

class CheeseStoryImage extends Image
{
  private $filterOptions;
  
  public function __construct($id, $resourceId, $options)
  {
    $this->filterOptions = $options;
    
    parent::__construct('cheesestory/'.$id, $resourceId);
  }
  
  public function getThumbnailDefinitions()
  {
    return array(
      new ThumbnailDefinition('160x160', new FilterChain(array(
        array(
          'id' => 'crop',
          'options' => $this->getFilterOptions('crop'),
          'resolver' => new CropFilterOptionsResolver()
        ),
        array(
          'id' => 'resize', 'options' => array('size' => '160x160'),
        )
      )))
    );
  }
  
  protected function getFilterOptions($id, $default=array())
  {
    return isset($this->filterOptions[$id]) ? $this->filterOptions[$id] : $default;
  }
}