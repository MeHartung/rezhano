<?php

namespace StoreBundle\Media\Store\Catalog\Product;

use Accurateweb\ImagingBundle\Filter\CropFilterOptionsResolver;
use Accurateweb\ImagingBundle\Filter\FilterChain;
use Accurateweb\MediaBundle\Model\Image\Image;
use Accurateweb\MediaBundle\Model\Thumbnail\ThumbnailDefinition;

class ProductImage extends Image
{
  private $filterOptions;
  
  public function __construct($id, $resourceId, $options)
  {
    $this->filterOptions = $options;
    
    parent::__construct('product/'.$id, $resourceId);
  }
  
  public function getThumbnailDefinitions()
  {
    return array(
      new ThumbnailDefinition('catalog_prev', new FilterChain(array(
        array(
          'id' => 'resize', 'options' => array('size' => 'x192'),
        )
      ))),
      new ThumbnailDefinition('160x160', new FilterChain(array(
        array(
          'id' => 'crop',
          'options' => $this->getFilterOptions('crop'),
          'resolver' => new CropFilterOptionsResolver()
        ),
        array(
          'id' => 'resize', 'options' => array('size' => '160x160'),
        )
      ))),/*
      new ThumbnailDefinition('last_view_prev', new FilterChain(array(
        array(
          'id' => 'resize', 'options' => array('size' => 'x140'),
        )
      )))*/
    );
  }
  
  protected function getFilterOptions($id, $default=array())
  {
    return isset($this->filterOptions[$id]) ? $this->filterOptions[$id] : $default;
  }
}