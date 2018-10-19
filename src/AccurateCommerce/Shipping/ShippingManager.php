<?php

namespace AccurateCommerce\Shipping;

use AccurateCommerce\Shipping\Method\ShippingMethod;
use AccurateCommerce\Shipping\Service\ShippingService;
use AccurateCommerce\Shipping\Shipment\Shipment;

/**
 * Предоставляет сведения о доступных способах доставки
 *
 * @author Dancy
 */
class ShippingManager
{
  const DEFAULT_PRIORITY_PICKUP = 1000;
  const DEFAULT_PRIORITY_COURIER = 10;
    
  private $shippingServices,
          $shippingMethods;
  
  public function __construct()
  {
    $this->shippingServices = array();
    $this->shippingMethods = array();
  }
  
  /**
   * Возвращает список известных способов доставки
   * 
   * @return ShippingMethod[]
   */
  /**
   * Возвращает набор способов доставки, отфильтрованный по заданному CLSID. Если CLSID не задан, возвращает
   * все дзарегистрированные способы доставки.
   * 
   * @param String $clsid CLSID способов доставки, которые требуется получить
   * @return ShippingMethod[] Набор способов доставки с заданным CLSID
   */
  public function getShippingMethods($clsid=null)
  {
    if (null === $clsid)
    {
      return $this->shippingMethods;
    }
    
    $shippingMethods = array();
    foreach ($this->shippingMethods as $shippingMethod)
    {
      if ($shippingMethod->getClsid() == $clsid)
      {
        $shippingMethods[] = $shippingMethod;
      }
    }
    
    return $shippingMethods;
  }
   
  /**
   * Возвращает набор доступных способов доставки для указанного отправления
   * 
   * @param Shipment $shipment Отправление, для которого производится поиск доступных способов отправки
   * @param String $clsid CLSID группы способов доставки
   * 
   * @return ShippingMethod[]
   */
  public function getAvailableShippingMethodsForShipment(Shipment $shipment, $clsid=null)
  {
    $availableShippingMethods = array();
    
    $shippingMethods = $this->getShippingMethods($clsid);
    foreach ($shippingMethods as $shippingMethod)
    {      
      if ($shippingMethod->isAvailable($shipment))
      {
        $availableShippingMethods[(string)$shippingMethod->getUid()] = $shippingMethod;
      }
    }
    
    return $this->filterAvailableShippingMethods($availableShippingMethods, $shipment);
  }
  
  /**
   * Выполняет регистрацию службы доставки в списке доступных служб доставки
   * 
   * В случае, если указанная служба уже зарегистрирована, выбрасывает исключение
   * 
   * @param ShippingService $service
   * @throws Exception
   */
  public function registerShippingService(ShippingService $service)
  {
    if ($this->isShippingServiceRegistered($service->getUid()))
    {
      throw new Exception(sprintf('Shipping service with id "%s" already registered', $service->getUid()));
    }
    
    $this->shippingServices[(string)$service->getUid()] = $service;
    
    $shippingMethods = $service->getShippingMethods();
    foreach ($shippingMethods as $shippingMethod)        
    {
      $this->shippingMethods[(string)$shippingMethod->getUid()] = $shippingMethod;
    }
  }
  
  /**
   * Возвращает true, если служба доставки зарегистрирована в системе, в противном случае false
   * 
   * @param String $uid
   * @return boolean
   */
  public function isShippingServiceRegistered($uid)
  {
    return isset($this->shippingServices[(string)$uid]);
  }
  
  /**
   * Возвращает объект способа доставки по заданному UID
   * 
   * @param String $uid
   * @return ShippingMethod
   */
  public function getShippingMethodByUid($uid)
  {
    return isset($this->shippingMethods[$uid]) ? $this->shippingMethods[$uid] : null;
  }
  
  /**
   * Выполняет фильтрацию доступных способов доставки в соответствии с требованиями бизнес-логики
   * 
   * @param ShippingMethod[] $availableShippingMethods
   * @param Shipment $shipment
   */
  public function filterAvailableShippingMethods($availableShippingMethods, $shipment)
  {
    $filteredShippingMethods = array();
    
    foreach ($availableShippingMethods as $availableShippingMethod)
    {
      $filteredShippingMethods[(string)$availableShippingMethod->getUid()] = $availableShippingMethod;      
    }
    
    return $filteredShippingMethods;
  }
  
  /**
   * Возвращает способ доставки, предлагаемый покупателю по умолчанию для указанного отправления
   * 
   * @param Shipment $shipment
   * @return ShippingMethod Способ доставки, который должен быть предложен покупателю по умолчанию для указанного отправления
   */
  public function getDefaultShippingMethodForShipment(Shipment $shipment)
  {
    $availableShippingMethods = $this->getAvailableShippingMethodsForShipment($shipment);
    
    //0. Если не из чего выбирать, то и возвращать нечего
    if (empty($availableShippingMethods))
    {
      return null;
    }

    //3. Вернем первый из оставшихся способов доставки, если это не "Другая транспортная компания"
    //Правда, если "Другая транспортная компания" - единственный из оставшихся способов, выберем-таки его
    $defaultShippingMethod = reset($availableShippingMethods);
//    if ($defaultShippingMethod->getUid() == ShippingMethodUserDefined::UID)
//    {
//      if (count($availableShippingMethods) > 1)
//      {
//        return next($availableShippingMethods);
//      }
//    }
    
    return $defaultShippingMethod;
  }
  
  /**
   * Возвращает таблицу приоритетов способов доставки
   * 
   * @return int[]
   */
  public static function getShippingMethodPriorityMap()
  {
    return array(       
//      ShippingMethodSpecregionPickup::UID => 2000,
//      ShippingMethodUserDefined::UID => 0,
//      ShippingMethodSpecregionCourier::UID => 100,
//      ShippingMethodDelLinCourier::UID => 90,
//      ShippingMethodKitCourier::UID => 80,
//      ShippingMethodPecomPickup::UID => 75,
//      ShippingMethodZheldorExpedicia::UID => 70,
//      ShippingMethodRatekSib::UID => 60,
    );
  }
  
  public static function sortByPriority(&$shippingMethods)
  {
    usort($shippingMethods, array('ShippingManager', 'compareShippingChoices'));
  }
  
  public static function getShippingMethodPriority(ShippingMethod $shippingMethod)
  {
    $priorityMap = self::getShippingMethodPriorityMap();
    
    return isset($priorityMap[$shippingMethod->getUid()]) ? $priorityMap[$shippingMethod->getUid()] : (
            $shippingMethod->getClsid() == ShippingMethod::CLSID_PICKUP ? self::DEFAULT_PRIORITY_PICKUP : self::DEFAULT_PRIORITY_COURIER);
  }
  
  /**
   * Сравнивает способы доставки таким образом, чтобы способ доставки "Другая транспортная компания" всегда был последним
   * 
   * @param ShippingMethod $a
   * @param ShippingMethod $b
   * @return int
   */
  protected static function compareShippingChoices($a, $b)
  {    
    $priorityA = self::getShippingMethodPriority($a);
    $priorityB = self::getShippingMethodPriority($b);
    
    if ($priorityA < $priorityB)
    {
      return 1;
    }
    if ($priorityA > $priorityB)
    {
      return -1;
    }
    return 0;
  }}


