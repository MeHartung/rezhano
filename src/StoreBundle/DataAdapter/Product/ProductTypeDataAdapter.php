<?php

namespace StoreBundle\DataAdapter\Product;

use Accurateweb\ClientApplicationBundle\DataAdapter\ClientApplicationModelAdapterInterface;
use Accurateweb\MediaBundle\Model\Media\Resource\MediaResource;
use StoreBundle\Entity\Store\Catalog\Product\Attributes\Type\ProductType;

class ProductTypeDataAdapter implements ClientApplicationModelAdapterInterface
{
  /**
   * @param $subject ProductType
   * @param array $options
   * @return array
   */
  public function transform ($subject, $options = array())
  {
    if ($subject === null) {
      return [];
    }
    /**
     * @var $media MediaResource
     */
    return array(
      'id' => $subject->getId(),
      'name' => $subject->getName(),
      'min_count' => $subject->getMinCount(),
      'count_step' => $subject->getCountStep(),
      'measured' => $subject->getMeasured(),
    );
  }

  public function getModelName ()
  {
    return 'ProductType';
  }

  public function supports ($subject)
  {
    return $subject instanceof ProductType;
  }
}