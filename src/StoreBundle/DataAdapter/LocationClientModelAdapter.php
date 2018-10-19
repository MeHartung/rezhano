<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 15.09.2017
 * Time: 18:27
 */

namespace StoreBundle\DataAdapter;


use AccurateCommerce\DataAdapter\ClientApplicationModelAdapterInterface;
use StoreBundle\Service\Geography\Location;

class LocationClientModelAdapter implements ClientApplicationModelAdapterInterface
{
  private $location;

  public function __construct(Location $location)
  {
    $this->location = $location;
  }

  function getClientModelName()
  {
    return 'Location';
  }

  function getClientModelValues($context = null)
  {
    $cdekCity = $this->location->getCdekCity();


    $city = array(
      'code' => $this->location->getAlias(),
      'name' => $this->location->getCityName(),
      'region' => $this->location->getRegionName()
    );

    return array(
      'code' => $this->location->getAlias(),
      'isConfirmed' => $this->location->isConfirmed(),
      'city' => $city
    );
  }

  function getClientModelId()
  {
    return $this->location->getAlias();
  }

}