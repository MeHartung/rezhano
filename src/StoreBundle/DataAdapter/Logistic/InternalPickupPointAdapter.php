<?php

namespace StoreBundle\DataAdapter\Logistic;


use Accurateweb\ClientApplicationBundle\DataAdapter\ClientApplicationModelAdapterInterface;
use StoreBundle\Entity\Store\Shipping\PickupPoint;

class InternalPickupPointAdapter implements ClientApplicationModelAdapterInterface
{
  /**
   * @param $pickupPoint PickupPoint
   * @return array
   */
  public function transform ($pickupPoint, $options = [])
  {
    if(($pickupPoint instanceof PickupPoint ) === false)
    {
      throw new \Exception('Required PickupPoint');
    }
    
    return array(
      'id' => $pickupPoint->getId(),
      'address' => $pickupPoint->getAddress(),
      'name' => $pickupPoint->getName(),
      'description' => $pickupPoint->getDescription(),
    );
  }
  
  public function getModelName ()
  {
    return 'InternalPickupPoint';
  }
  
  public function supports ($subject)
  {
    return $subject instanceof PickupPoint;
  }
}
