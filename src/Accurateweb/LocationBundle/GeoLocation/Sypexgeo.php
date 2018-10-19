<?php

namespace Accurateweb\LocationBundle\GeoLocation;

class Sypexgeo implements GeoInterface
{
  private $ip;
  private $charset;

  private $data = null;

  public function __construct ($ip)
  {
    $this->ip = $ip;
    $this->charset = 'UTF-8';
  }

  public function getCountryIso()
  {
    $data = $this->getData();

    if (isset($data['country']))
    {
      return $data['country']['iso'];
    }

    return null;
  }

  public function getCityName()
  {
    $data = $this->getData();

    if (isset($data['city']))
    {
      return $data['city']['name_ru'];
    }

    return null;
  }

  /**
   * функция возвращет конкретное значение из полученного массива данных по ip
   *
   * @param string $key - ключ массива. Если интересует конкретное значение.
   * @return mixed
   *
   * Ключ может быть равным 'inetnum', 'country', 'city', 'region', 'district', 'lat', 'lng'
   */
  public function getValue ($key)
  {
    $data = $this->getData();

    return isset($data[$key]) ? $data[$key] : null;
  }

  public function getData()
  {
    if (!is_null($this->data))
    {
      return $this->data;
    }

    $this->data = $this->getGeobaseData();
    return $this->data;
  }

  /**
   * функция получает данные по ip.
   *
   * @return array - возвращает массив с данными
   */
  protected function getGeobaseData ()
  {
    // получаем данные по ip
    $link = 'http://api.sypexgeo.net/json/' . $this->ip;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $link);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
    $string = curl_exec($ch);
    curl_close($ch);

    $this->data = @\json_decode($string, true);

    return $this->data;
  }
}
