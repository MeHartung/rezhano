<?php

namespace Accurateweb\LocationBundle\GeoLocation;

class IpGeoBase implements GeoInterface
{
  private $ip;
  private $charset;

  private $data = null;

  public function __construct ($ip)
  {
    $this->ip = $ip;
    $this->charset = 'UTF-8';
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

  public function getCountryIso ()
  {
    return $this->getValue('country');
  }

  public function getCityName ()
  {
    return $this->getValue('city');
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
    $link = 'ipgeobase.ru:7020/geo?ip=' . $this->ip;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $link);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
    $string = curl_exec($ch);
    curl_close($ch);

    // если указана кодировка отличная от windows-1251, изменяем кодировку
    if ($this->charset)
    {
      $string = iconv('Windows-1251', $this->charset, $string);
    }

    $this->data = $this->parse_string($string);

    return $this->data;
  }

  /**
   * функция парсит полученные в XML данные в случае, если на сервере не установлено расширение Simplexml
   *
   * @return array - возвращает массив с данными
   */
  private function parse_string ($string)
  {
    $pa['inetnum'] = '#<inetnum>(.*)</inetnum>#is';
    $pa['country'] = '#<country>(.*)</country>#is';
    $pa['city'] = '#<city>(.*)</city>#is';
    $pa['region'] = '#<region>(.*)</region>#is';
    $pa['district'] = '#<district>(.*)</district>#is';
    $pa['lat'] = '#<lat>(.*)</lat>#is';
    $pa['lng'] = '#<lng>(.*)</lng>#is';
    $data = array();

    foreach ($pa as $key => $pattern)
    {
      preg_match($pattern, $string, $out);

      if (isset($out[1]) && $out[1])
      {
        $data[$key] = trim($out[1]);
      }
    }

    return $data;
  }
}
