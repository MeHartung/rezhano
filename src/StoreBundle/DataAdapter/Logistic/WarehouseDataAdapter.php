<?php

namespace StoreBundle\DataAdapter\Logistic;

use Accurateweb\ClientApplicationBundle\DataAdapter\ClientApplicationModelAdapterInterface;
use StoreBundle\Entity\Store\Logistics\Warehouse\Warehouse;

class WarehouseDataAdapter implements ClientApplicationModelAdapterInterface
{
  /**
   * @param $subject Warehouse
   * @param array $options
   * @return array|void
   */
  public function transform ($subject, $options = array())
  {
    return [
      'id' => $subject->getId(),
      'name' => $subject->getName(),
      'address' => $subject->getAddress(),
      'cityName' => $subject->getCity()->getName(),
      'latitude' => $subject->getLatitude(),
      'longitude' => $subject->getLongitude(),
    ];
  }

  public function getModelName ()
  {
    return 'Warehouse';
  }

  public function supports ($subject)
  {
    return $subject instanceof Warehouse;
  }
}