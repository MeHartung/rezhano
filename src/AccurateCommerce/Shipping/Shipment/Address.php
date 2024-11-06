<?php

namespace AccurateCommerce\Shipping\Shipment;

/**
 * Предоставляет функции вычисления различных характеристик и кодов, связанных с адресом
 *
 * @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
class Address
{
  private $postCode;
  private $cityName;
  private $cityFiasUid;
  private $address;
  
  private $cityFiasRecord;

  private $isInPresenceCity;

  /**
   * Конструктор
   * 
   * @param String $postCode    Почтовый индекс
   * @param String $cityName    Название города
   * @param String $cityFiasUid AOGUID города в БД ФИАС
   * @param String $address     Адрес
   */
  public function __construct($postCode, $cityName, $cityFiasUid, $address)
  {
    $this->postCode = $postCode;
    $this->cityName = $cityName;
    $this->cityFiasUid = $cityFiasUid;
    $this->address = $address;
  }
  
  /**
   * Возвращает AOGUID города в БД ФИАС
   * 
   * @return string
   */
  public function getCityFiasUid()
  {
    return $this->cityFiasUid;
  }
  
  /**
   * Возвращает код КЛАДР корода
   * 
   * @return string
   */
  public function getCityKladrCode()
  {
    $fiasRecord = $this->getCityFiasRecord();
    if ($fiasRecord)
    {
      /* @var $fiasRecord BaseFiasAddressObject */
      return $fiasRecord->getCode();
    }
    
    return null;
  }
  
  /**
   * Возвращает код региона города или null, если информация о городе на найдена в таблице ФИАС
   * 
   * @return int | null
   */
  public function getCityRegionCode()
  {
    $fiasRecord = $this->getCityFiasRecord();
    if ($fiasRecord)
    {
      /* @var $fiasRecord BaseFiasAddressObject */
      return $fiasRecord->getRegioncode();
    }
    
    return null;    
  }
  
  /**
   * Возвращает название города назначения
   * 
   * @return String
   */
  public function getCityName()
  {
    return $this->cityName;
  }
  
  /**
   * Возвращает адрес в городе назначения
   * 
   * @return String
   */
  public function getAddress()
  {
    return $this->address;
  }
  
  /**
   * Возвращает почтовый индекс. 
   * 
   * Если почтовый индекс не был задан явно, возвращает почтовый индекс главного отделения в указанном городе.
   * 
   * @return String
   */
  public function getPostCode()
  {
    if (null === $this->postCode)
    {
      $fiasRecord = $this->getCityFiasRecord();
      if ($fiasRecord)
      {
        /* @var $fiasRecord BaseFiasAddressObject */
        $this->postCode = $fiasRecord->getPostalcode();
      }
    }
    
    return $this->postCode;
  }
  
  /**
   * Возвращает запись ФИАС для данного города
   * 
   * @return FiasAddressObject
   */
  protected function getCityFiasRecord()
  {
//    if (null === $this->cityFiasRecord)
//    {
//      $c = new Criteria();
//      $c->add(FiasAddressObjectPeer::AOGUID, $this->cityFiasUid);
//
//      $this->cityFiasRecord = FiasAddressObjectPeer::doSelectOne($c);
//    }
    
    return $this->cityFiasRecord;
  }

  /**
   * Возвращает true, если адрес находится в городе присутствия Спецрегион
   */
  public function isInPresenceCity()
  {
    return false;
//    if (null === $this->isInPresenceCity)
//    {
//      $this->isInPresenceCity =  CityQuery::create()
//          ->innerJoinDepartment()
//          ->filterByFiasAoguid($this->getCityFiasUid())
//          ->limit(1)
//          ->count() > 0;
//    }
//
//    return $this->isInPresenceCity;
  }
}
