<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 25.08.17
 * Time: 17:51
 */

namespace StoreBundle\Media\Text;

use Accurateweb\ImagingBundle\Filter\CropFilterOptionsResolver;
use Accurateweb\ImagingBundle\Filter\FilterChain;
use Accurateweb\MediaBundle\Model\Image\Image;
use Accurateweb\MediaBundle\Model\Thumbnail\ThumbnailDefinition;

class SpecialOfferImage extends Image
{
  private $filterOptions;

  public function __construct($id, $resourceId, $options)
  {
    $this->filterOptions = $options;

    parent::__construct('specialOffer/'.$id, $resourceId);
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