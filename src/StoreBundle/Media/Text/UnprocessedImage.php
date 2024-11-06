<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Media\Text;

use Accurateweb\ImagingBundle\Filter\CropFilterOptionsResolver;
use Accurateweb\ImagingBundle\Filter\FilterChain;
use Accurateweb\MediaBundle\Model\Image\Image;
use Accurateweb\MediaBundle\Model\Thumbnail\ThumbnailDefinition;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UnprocessedImage extends Image
{
  public function __construct($id, $resourceId, $options)
  {
    parent::__construct($id, $resourceId, $options);
  }

  protected function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefault('crop', []);
  }

  public function getThumbnailDefinitions()
  {
    return array(
//      new ThumbnailDefinition('160x160', new FilterChain(array(
//        array(
//          'id' => 'crop',
//          'options' => $this->getOption('crop'),
//          'resolver' => new CropFilterOptionsResolver()
//        ),
//        array(
//          'id' => 'resize', 'options' => array('size' => '160x160'),
//        )
//      )))
    );
  }
}