<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 15.09.2017
 * Time: 18:21
 */

namespace StoreBundle\DataAdapter;


use AccurateCommerce\DataAdapter\ClientApplicationModelAdapterInterface;
use StoreBundle\Entity\Store\Logistics\Delivery\Cdek\CdekCity;

class CityClientModelAdapter implements ClientApplicationModelAdapterInterface
{
  private $city;

  public function __construct(CdekCity $city)
  {
    $this->city = $city;
  }

  function getClientModelName()
  {
    return 'City';
  }

  function getClientModelValues($context = null)
  {
    return array(
      'name' => $this->city->getName(),
      'code' => $this->city->getCode()
    );
  }

  function getClientModelId()
  {
    return $this->city->getCode();
  }

}