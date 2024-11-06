<?php

/*
 *  @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */

namespace AccurateCommerce\Component\CdekShipping\Api;

use AccurateCommerce\Shipping\Shipment\Address;
use StoreBundle\Entity\Store\Logistics\Delivery\Cdek\CdekRawPvzlist;

/**
 * API калькулятора тарифов СДЭК
 *
 * @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
class CdekApiClient
{
  /*
   * Версия используемого API
   */
  const API_VERSION = "1.0";
  /*
   * Адрес для отправки запросов
   */
  const CALCULATOR_URI = "http://api.cdek.ru/calculator/calculate_price_by_json.php";
  const CITY_LIST_URI = "http://api.cdek.ru/city/getListByTerm/json.php";

  const METHOD_STATUS_REPORT = 'status_report_h.php';
  const METOD_PVZLIST = 'pvzlist.php';

  /*
   * Идентификатор города отправления (Екатеринбург)
   */
  const SENDER_CITY_ID = 250;

  const PVZTYPE_PVZ = 'PVZ';
  const PVZTYPE_POSTOMAT = 'POSTOMAT';
  const PVZTYPE_ALL = 'ALL';

  //Посылка склад-склад
  const TARIFF_PARCEL_STORAGE_STORAGE = 136;
  //Посылка склад-дверь
  const TARIFF_PARCEL_STORAGE_DOOR = 137;
  //Посылка дверь-склад
  const TARIFF_PARCEL_DOOR_STORAGE = 138;
  //Посылка дверь-дверь
  const TARIFF_PARCEL_DOOR_DOOR = 139;

  private $login;

  private $apiResponse;

  private $integrationApiUrl;

  public function __construct($login, $password, $integrationApiUrl)
  {
    $this->login = $login;
    $this->password = $password;
    $this->integrationApiUrl = $this->removeTrailingSlashes($integrationApiUrl);
  }

  /**
   * Выполнение POST-запроса на сервер для получения данных
   * по запрашиваемым параметрам.
   *
   *
   */
  private function doPostQuery($uri, $data)
  {
    if ($this->login && $this->password)
    {
      $date = date('Y-m-d');

      $data['authLogin'] = $this->login;
      $data['secure'] = md5($date . '&' . $this->password);
      $data['dateExecute'] = $date;
    }

    $data_string = json_encode($data);

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $uri);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json'
    ));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

    $result = curl_exec($ch);
    curl_close($ch);

    return json_decode($result, true);
  }

  private function doGetRequest($uri, $parameters = array())
  {
    $ch = curl_init();

    $requestUri = $uri;

    $queryString = http_build_query($parameters);
    if ($queryString)
    {
      $requestUri .= '?' . $queryString;
    }

    curl_setopt($ch, CURLOPT_URL, $requestUri);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);

    $response = curl_exec($ch);

    curl_close($ch);

    return $response;
  }

  private function doIntegrationGetQuery($uri, $parameters, $securityDate = null)
  {
    if ($this->login && $this->password)
    {
      $date = null !== $securityDate ? $securityDate : date('Y-m-d');
      $parameters = array_merge(array(
        'account' => $this->login,
        'secure' => md5($date . '&' . $this->password)
      ), $parameters);
    }

    $ch = curl_init();

    $requestUri = $uri;

    $queryString = http_build_query($parameters);
    if ($queryString)
    {
      $requestUri .= '?' . $queryString;
    }

    curl_setopt($ch, CURLOPT_URL, $requestUri);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);

    $response = curl_exec($ch);

    curl_close($ch);

    return $response;
  }

  public function getCityId()
  {
    $cityId = null;

    $destinationPostcode = $this->getToPostIndex();

    $response = $this->doGetRequest(self::CITY_LIST_URI, array('q' => $this->getToCity()));
    if (isset($response['geonames']) && is_array($response['geonames']) && !empty($response['geonames']))
    {
      foreach ($response['geonames'] as $geoname)
      {
        if (is_array($geoname['postCodeArray']) && in_array($destinationPostcode, $geoname['postCodeArray']))
        {
          $cityId = $geoname['id'];
          break;
        }
      }
    }

    return $cityId;
  }

  public function getCityPostcodes($cityName)
  {
    $response = json_decode($this->doGetRequest(self::CITY_LIST_URI, array('q' => $cityName)), true);
    if (isset($response['geonames']) && is_array($response['geonames']) && !empty($response['geonames']))
    {
      foreach ($response['geonames'] as $geoname)
      {
        if (is_array($geoname['postCodeArray']))
        {
          return $geoname['postCodeArray'];
        }
      }
    }

    return null;
  }

  public function getShippingInfo(Address $source, Address $destination, $weight, $volume, $tariffId)
  {
    $data = array(
      'version' => self::API_VERSION,
      'senderCityPostCode' => $source->getPostCode(),
      'receiverCityPostCode' => $destination->getPostCode(),
      'tariffId' => $tariffId,
      'goods' => array(
        array(
          'weight' => (float)sprintf('%.3f', $weight <= 0 ? 1.000 : (float)$weight / 1000),
          'volume' => $volume ?: 0.01
        )
      )
    );

    $response = $this->doPostQuery(self::CALCULATOR_URI, $data);

    $apiResponse = isset($response['result']) ? $response['result'] : null;

    return CdekShippingInfo::fromApiResponse($apiResponse);
  }

  public function getTrackInfo()
  {
    $trackInfo = null;

    $sendDate = date('Y-m-d', $this->getSentAt());

    $apiResponseXml = $this->doIntegrationGetQuery($this->getIntegrationMethodUrl(self::METHOD_STATUS_REPORT), array(
      'showhistory' => 1,
      'dispatchnumber' => $this->getTrackingNumber(),
      'datefirst' => $sendDate
    ), $sendDate);

    if ($apiResponseXml)
    {
      libxml_use_internal_errors(true);

      $xml = simplexml_load_string($apiResponseXml);
      $stateHistoryXml = $xml->Order->Status->State;

      $trackInfo = new TrackingServiceResponse($this->getTrackingNumber(), true);
      $track = array();
      foreach ($stateHistoryXml as $stateHistoryNode)
      {
        $track[] = array(
          'time' => (string)$stateHistoryNode['Date'],
          'attr' => (string)$stateHistoryNode['Description'],
          'place' => (string)$stateHistoryNode['CityName'],
          'type' => null,
          'type_id' => null,
          'attr_id' => null,
          'place_index' => null
        );
      }
      $trackInfo->setTrack($track);
    }

    return $trackInfo;
  }


  /**
   * Возвращает список пунктов выдачи заказов
   *
   * @param int $cityId
   * @param int $cityPostCode
   * @param string $type
   * @param string $lang
   *
   * @return PvzListItem[]
   */
  public function getPvzList($cityId = null, $cityPostCode = null, $type = null, $lang = null)
  {
    $pvzlist = [];
    $parameters = [];

    if (isset($cityId))
    {
      $parameters['cityid'] = $cityId;
    }
    if (isset($cityPostCode))
    {
      $parameters['citypostcode'] = $cityPostCode;
    }
    if (isset($type))
    {
      $parameters['type'] = $type;
    }
    if (isset($lang))
    {
      $parameters['lang'] = $lang;
    }

    $data = $this->doGetRequest($this->getIntegrationMethodUrl(self::METOD_PVZLIST), $parameters);
    //@TODO: Добавить обработку ошибок
    $xml = simplexml_load_string($data);
    //@TODO: Добавить обработку ошибок
    foreach ($xml as $pvzRecord)
    {
      $attributes = $pvzRecord->attributes();

      $pvzListItem = new CdekRawPvzlist();
      $pvzListItem->fromArray($attributes);

      $pvzlist[] = $pvzListItem;
    }

    return $pvzlist;
  }

  private function getIntegrationMethodUrl($method)
  {
    return $this->integrationApiUrl . '/' . $method;
  }

  private function removeTrailingSlashes($url)
  {
    return preg_replace('/\/+$/', '', $url);
  }
}
