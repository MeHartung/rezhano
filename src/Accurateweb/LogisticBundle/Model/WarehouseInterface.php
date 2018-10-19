<?php

namespace Accurateweb\LogisticBundle\Model;


interface WarehouseInterface
{
  /**
   * @return string
   */
  public function getName();

  /**
   * @return string
   */
  public function getAddress();

  /**
   * @return string
   */
  public function getLatitude();

  /**
   * @return string
   */
  public function getLongitude();

  /**
   * @return CityInterface
   */
  public function getCity();

  /**
   * @return integer
   */
  public function getId();
}