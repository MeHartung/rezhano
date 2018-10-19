<?php

namespace AccurateCommerce\Shipping\Pickup;

use AccurateCommerce\Shipping\Estimate\ShippingEstimate;

class StaticPickupPoint implements PickupPointInterface
{
  private $name;
  private $shippingEstimate;
  private $coordinates;
  private $address;
  private $timetable;
  private $phoneNumber;

  public function __construct ($name, ShippingEstimate $shippingEstimate=null, $coordinates=null, $address=null, $timetable=null, $phoneNumber=null)
  {
    $this->name = $name;
    $this->shippingEstimate = $shippingEstimate;
    $this->coordinates = $coordinates;
    $this->address = $address;
    $this->phoneNumber = $phoneNumber;
    $this->timetable = $timetable;
  }

  public function getAcceptedCreditCards ()
  {
    return [];
  }

  public function getAddress ()
  {
    if ($this->address)
    {
      return '';
    }

    return $this->address;
  }

  public function getGeoCoordinates ()
  {
    if (!$this->coordinates)
    {
      return [];
    }

    return $this->coordinates;
  }

  public function getName ()
  {
    return $this->name;
  }

  public function getTimetable ()
  {
    if (!$this->timetable)
    {
      return '';
    }

    return $this->timetable;
  }

  public function getPhoneNumber ()
  {
    if (!$this->phoneNumber)
    {
      return '';
    }

    return $this->phoneNumber;
  }

  public function getShippingEstimate (\AccurateCommerce\Shipping\Pickup\Shipment $shipment)
  {
    if (!$this->shippingEstimate)
    {
      $this->shippingEstimate = new ShippingEstimate(null, null);
    }

    return $this->shippingEstimate;
  }
}