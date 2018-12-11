<?php

namespace StoreBundle\DataAdapter\Store;


use Accurateweb\ClientApplicationBundle\DataAdapter\ClientApplicationModelAdapterInterface;
use Accurateweb\MediaBundle\Model\Media\Storage\MediaStorageInterface;
use StoreBundle\Entity\Store\Store;

class StoreDataAdapter implements ClientApplicationModelAdapterInterface
{
  private $mediaStorage;

  public function __construct (MediaStorageInterface $mediaStorage)
  {
    $this->mediaStorage = $mediaStorage;
  }

  /**
   * @param Store $subject
   * @param array $options
   * @return array
   */
  public function transform ($subject, $options = array())
  {
    $image = $subject->getImage();
    $imageUrl = null;

    if ($image && $this->mediaStorage->exists($image))
    {
      $imageUrl = $this->mediaStorage->retrieve($image)->getUrl();
    }

    return [
      'name' => $subject->getName(),
      'address' => $subject->getAddress(),
      'latitude' => $subject->getLatitude(),
      'longitude' => $subject->getLongitude(),
      'showFooter' => $subject->isShowFooter(),
      'phone' => $subject->getPhone(),
      'workTime' => $subject->getWorkTime(),
      'image' => $imageUrl,
    ];
  }

  public function getModelName ()
  {
    return 'Store';
  }

  public function supports ($subject)
  {
    return $subject instanceof Store;
  }

}