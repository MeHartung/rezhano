<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 11.10.2017
 * Time: 18:59
 */

namespace StoreBundle\Service\Geography;


class PresenceMap
{
  private $map = [
    'moskow' => [
      'name' => 'Москва',
      'phone' => '+7 (902) 1-555-794',
      'address' => 'г. Москва, ул. Зюзинская, д.6, к.2',
      'cdekCityCode' => 44,
      'postcode' => 101000
    ],
    'ekb' => [
      'name' => 'Екатеринбург',
      'phone' => '+7 (343) 207-73-37',
      'address' => 'г. Екатеринбург, ул. К.Либкнехта д.23Б, ТЦ &laquo;Sila Voli&raquo;, 1 этаж',
      'cdekCityCode' => 250,
      'postcode' => 620000
    ]
  ];

  public function getAll()
  {
    return $this->map;
  }

  public function findByCityCode($code)
  {
    foreach ($this->map as $i => $entry)
    {
      if ($entry['cdekCityCode'] == $code)
      {
        return $entry;
      }
    }

    return null;
  }

  public function get($key)
  {
    return isset($this->map[$key]) ? $this->map[$key] : null;
  }
}