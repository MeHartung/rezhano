<?php

namespace Accurateweb\LocationBundle\Model;

class UserLocation
{
  private $cityName;

  private $cityCode;

  private $countryCode;

  /**
   * @return mixed
   */
  public function getCityName ()
  {
    return $this->cityName;
  }

  /**
   * @param mixed $cityName
   * @return $this
   */
  public function setCityName ($cityName)
  {
    $this->cityName = $cityName;
    return $this;
  }

  /**
   * @return mixed
   */
  public function getCityCode ()
  {
    return $this->cityCode;
  }

  /**
   * @param mixed $cityCode
   * @return $this
   */
  public function setCityCode ($cityCode)
  {
    $this->cityCode = $cityCode;
    return $this;
  }

  /**
   * @return mixed
   */
  public function getCountryCode ()
  {
    return $this->countryCode;
  }

  /**
   * @param mixed $countryCode
   * @return $this
   */
  public function setCountryCode ($countryCode)
  {
    $this->countryCode = $countryCode;
    return $this;
  }
}