<?php

/*
 * Автор Денис Н. Рагозин <dragozin at accurateweb.ru>
 */
namespace StoreBundle\Service\Geography;

use Doctrine\ORM\EntityManager;
use StoreBundle\Entity\Store\Logistics\Delivery\Cdek\CdekCity;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * 
 *
 * @author Денис Н. Рагозин <dragozin at accurateweb.ru>
 */
class Location
{
  const DEFAULT_PHONE = '+7 (902) 1-555-794';
  const DEFAULT_CITY_CODE = 'ekb';
  
  private $cityName,
          $contactPhone,
          $address,
          $isPresenceCity,
          $cdekCity,
          $isConfirmed,
          $cityCode,
          $cityPostcode = false, //По умолчанию false, как флаг необходимости загрузки почтового индекса. После загрузки будет иметь значение или null
          $presenceMap,
          $session,
          $geo;

  private $cdekApiClient;
  private $userLocation;

  /**
   * @var EntityManager
   */
  private $em;

  public function __construct(EntityManager $em, \Accurateweb\LocationBundle\Service\Location $awLocation)
  {
    $this->em = $em;
    $this->isConfirmed = false;
    $this->presenceMap = new PresenceMap();
    $this->awLocation = $awLocation;
    $this->userLocation = $this->awLocation->getLocation();
    $this->cityCode = $this->userLocation->getCityCode();
    $this->cityName = $this->userLocation->getCityName();

    if ($this->cityCode)
    {
      $presenceMap = $this->getPresenceMap();

      $entry = $presenceMap->findByCityCode($this->cityCode);

      if (null !== $entry)
      {
        $this->fromPresenceMapEntry($entry);
        return;
      }

      $repo = $this->em->getRepository('StoreBundle:Store\Logistics\Delivery\Cdek\CdekCity');
      $city = $repo->findOneBy(['code' => $this->cityCode]);

      if ($city)
      {
        $this->fromCdekCity($city);
        return;
      }
    }
  }
  /**
   * Возвращает город СДЭК
   * 
   * @return CdekCity
   */
  public function getCdekCity()
  {
    return $this->cdekCity;
  }
  
  /**
   * Возвращает название города
   * 
   * @return String
   */
  public function getCityName()
  {
    return $this->cityName;
  }

  /**
   * Возвращает почтовый индекс глав. почтамта города
   *
   * @return mixed
   */
  public function getCityPostcode()
  {
    if (false === $this->cityPostcode)
    {
      $this->cityPostcode = $this->detectCityPostcode();
    }

    return $this->cityPostcode;
  }

  /**
   * Возвращает контактный телефон
   * 
   * @return String
   */
  public function getContactPhone()
  {
    return $this->contactPhone;
  }

  public function getContactPhoneByCity($city)
  {
    $city = $this->getPresenceMap()->get($city);

    return $city ? $city['phone'] : self::DEFAULT_PHONE;
  }
  
  /**
   * Возвращает адрес офиса в городе присутствия
   * 
   * @return String
   */
  public function getAddress()
  {
    return $this->address;
  }
  
  public function isPresenceCity()
  {
    return $this->isPresenceCity;
  }
  
  public function getPresenceMap()
  {
    return $this->presenceMap;
  }
  
  private function fromPresenceMapEntry($presenceMapEntry)
  {    
    $this->cityName = $presenceMapEntry['name'];
    $this->contactPhone = $presenceMapEntry['phone'];
    $this->address = $presenceMapEntry['address'];
    $this->isPresenceCity = true;
    $this->cityCode = $presenceMapEntry['cdekCityCode'];
    $this->cityPostcode = $presenceMapEntry['postcode'];
    $this->loadCdekCity($presenceMapEntry['cdekCityCode']);
  }   
  
  private function fromCdekCity(CdekCity $city)
  {
    $this->cityName = $city->getName();
    $this->contactPhone = self::DEFAULT_PHONE;
    $this->address = null;
    $this->isPresenceCity = null !== $this->getPresenceMap()->findByCityCode($city->getCode());
    $this->cdekCity = $city;
    $this->cityCode = $city->getCode();
  }
  
  public function isConfirmed()
  {
    return $this->isConfirmed;
  }
  
  public function getAlias()
  {
    return $this->cityCode;
  }
  
  protected function loadCdekCity($cityCode)
  {
    $em = $this->getEntityManager();
    
    $repo = $em->getRepository('StoreBundle:Store\Logistics\Delivery\Cdek\CdekCity');

    $this->cdekCity = $repo->findOneBy(['code' => $cityCode]);       
  }
  
  /**
   * 
   * @return \Doctrine\ORM\EntityManager
   */
  private function getEntityManager()
  {
    return $this->em;
  }
  
  public function getRegionName()
  {
    $regionName = null;
    
    $cdekCity = $this->getCdekCity();
    if ($cdekCity)
    {
      $regionName = $cdekCity->getRegion();
    }
    
    return $regionName;
  }

  public function detectCityPostcode()
  {
    return null;
//    $postcode = $this->cdekApiClient->getCityPostcodes($this->getCityName());
//    if (is_array($postcode))
//    {
//      return reset($postcode);
//    }
//
//    return null;
  }

  public function getCityNameByPostcode($postcode)
  {
    $city = null;

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "https://api.print-post.com/api/index/v2/?index=".$postcode);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $output = curl_exec($ch);

    curl_close($ch);

    if ($output)
    {
      $output = json_decode($output, true);

      if (false !== $output && isset($output['city']) && isset($output['region']))
      {
        $city = $output['city'] ?: $output['region']; # потому что ПР присылает на г.Москва {city: "", region: "Москва"}
      }
    }

    return $city;
  }

}
