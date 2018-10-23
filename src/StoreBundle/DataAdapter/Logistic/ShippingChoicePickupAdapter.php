<?php

namespace AppBundle\DataAdapter\Logistic;

use AccurateCommerce\Shipping\Method\ShippingMethod;
use AccurateCommerce\Shipping\Pickup\PickupPointInterface;
use Doctrine\ORM\EntityManager;

class ShippingChoicePickupAdapter extends ShippingChoiceAdapter
{
  private $em;

  public function __construct (EntityManager $entityManager)
  {
    $this->em = $entityManager;
  }

  /**
   * @param $shippingMethod ShippingMethod
   * @return array
   */
  public function transform ($shippingMethod, $options = [])
  {
    $data = parent::transform($shippingMethod, $options);
    return $data;

//    if (empty($data))
//    {
//      return array();
//    }
//
//    if (!isset($options['pickup_point']) || !$options['pickup_point'] instanceof PickupPointInterface)
//    {
//      throw new \Exception('Required PickupPoint');
//    }
//
//    $pickupPoint = $options['pickup_point'];
//    $city = $this->em->getRepository('StoreBundle:Store\Logistics\Delivery\Cdek\CdekCity')->findOneBy(['name' => $pickupPoint->getCityName()]);
//    $country = $city ? $city->getCountry() : null;
//
//    return array_replace($data, [
//      'id' => $pickupPoint->getCode(),
//      'address' => $pickupPoint->getAddress(),
//      'fullAddress' => $pickupPoint->getFullAddress(),
//      'timetable' => $pickupPoint->getTimetable(),
//      'phone' => $pickupPoint->getPhoneNumber(),
//      'acceptedCards' => $pickupPoint->getAcceptedCreditCards(),
//      'geocoordinates' => $pickupPoint->getGeoCoordinates(),
//      'name' => $pickupPoint->getName(),
//      'city' => $pickupPoint->getCityName(),
//      'countryCode' => $country ? $country->getId() : null,
//      'countryName' => $country ? $country->getName() : null
//    ]);
  }

  public function getModelName ()
  {
    return 'ShippingChoicePickup';
  }

  public function supports ($subject)
  {
    return $subject instanceof ShippingMethod && $subject->getClsid() === ShippingMethod::CLSID_PICKUP;
  }
}
