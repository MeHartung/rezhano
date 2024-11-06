<?php

namespace AccurateCommerce\Shipping\Shipment;

use AccurateCommerce\Shipping\Shippable\ShippableInterface;
use StoreBundle\Entity\Store\Order\Order;

/**
 * Почтовое тправление, доставляемое клиенту
 *
 * @author @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
class Shipment
{
  /** @var Order */
  private $order;
  /** @var IShippable[] */
  private $shippables;
  /** @var Address */
  private $source;
  /** @var Address */  
  private $destination;
  
  private $shippingMethodSpecifics = array();
  
  /**
   * Конструктор
   * 
   * @param Order $order
   * @param ShippableInterface[] $shippables
   * @param Address $source
   * @param Address $destination
   */
  public function __construct($order, $shippables, Address $source, Address $destination)
  {
    $this->destination = $destination; 
    $this->source = $source;
    $this->shippables = $shippables;
    $this->order = $order;
  }
  
  /**
   * Возвращает суммарный вес посылки в кг
   * 
   * @return float
   */
  public function getWeight()
  {
    $weight = 0;
    foreach ($this->shippables as $shippable)
    {
      if ($shippable->getWeight() > 0) 
      {
        $weight += $shippable->getWeight(); 
      }
    }
    
    return $weight;
  }
  
  /**
   * Возвращает количество товаров, для которых не указан вес
   * 
   * @return int
   */
  public function getNonScaledShippableCount()
  {
    return count($this->getNonScaledShippables());
  }
  
  /**
   * Возвращает массив товаров, для которых не указан вес
   * 
   * @return Shippable[]
   */
  public function getNonScaledShippables()
  {
    $nonScaledShippables = array();
    
    foreach ($this->shippables as $shippable)
    {
       /*
        * На складе пытаются обмануть систему и, если кладовщику лень взвешить товар 
        * при приемке, он вбивает 1 грамм. Мы расцениваем такие товары, как не взвешенные
        */
       if ($shippable->getWeight() <= 0.001)
       {
         $nonScaledShippables[] = $shippable;
       }
    }
    
    return $nonScaledShippables;
  }
  
  /**
   * Возвращает суммарный объем посылки в м<sup>3</sup>
   * 
   * @return long
   */
  public function getVolume()
  {
    $volume = 0;
    foreach ($this->shippables as $shippable)
    {
      if ($shippable->getVolume() >= 0)
      {
        $volume += $shippable->getVolume();
      }
    }
    
    return $volume;
  }
  
  /**
   * Возвращает количество товаров в отправлении, для которых не указан объем
   * 
   * @return int
   */
  public function getNonSizedShippableCount()
  {
    return count($this->getNonSizedShippables());
  }
  
  /**
   * Возвращает массив товаров в отправлении, для которых не указан объем
   */
  public function getNonSizedShippables()
  {
    $nonSizedShippables = array();
    
    foreach ($this->shippables as $shippable)
    {
       if ($shippable->getVolume() <= 0)
       {
         $nonSizedShippables[] = $shippable;
       }
    }
    
    return $nonSizedShippables;
  }
  
  /**
   * Возвращает адрес пункта назначения отправления
   * 
   * @return Address
   */
  public function getDestination()
  {
    return $this->destination;
  }
  
  /**
   * Возвращает адрес отправителя
   * 
   * @return Address
   */
  public function getSource()
  {
    return $this->source;
  }
  
  //Это все для кеширования расчетов API
  /**
   * Эта функция позволяет сохранить в данных отправления данные, специфичные для поставщика услуг доставки с тем,
   * чтобы не получать их заново от поставщика услуг доставки
   * 
   * @param String $shippingMethodUid Идентификатор способа доставки
   * @param String $name Название данных / ключ
   * @param mixed $v Данные
   */
  public function addShippingMethodSpecific($shippingMethodUid, $name, $v)
  {
    if (!isset($this->shippingMethodSpecifics[$shippingMethodUid]))
    {
      $this->shippingMethodSpecifics[$shippingMethodUid] = array();
    }
    $this->shippingMethodSpecifics[$shippingMethodUid][$name] = $v;
  }
  
  /**
   * Возвращает ранее сохраненные в отправлении данные, специфичные для поставщика услуг доставки
   * 
   * @param String $shippingMethodUid Идентификатор способа доставки
   * @param String $name Название данных / ключ
   * 
   * @return mixed Сохраненные с заданным названием/ключом данные поставщика услуг доставки
   */
  public function getShippingMethodSpecific($shippingMethodUid, $name)
  {
    return isset($this->shippingMethodSpecifics[$shippingMethodUid][$name]) ? 
                         $this->shippingMethodSpecifics[$shippingMethodUid][$name] :  null;
  }
  
  /**
   * Удаляет все сохраненные данные поставщика услуг доставки
   * 
   * @param String $shippingMethodUid Идентификатор способа доставки
   */
  public function removeShippingMethodSpecific($shippingMethodUid)
  {
    unset($this->shippingMethodSpecifics[$shippingMethodUid]);
  }
  
  /**
   * Возвращает объект заказа, для которого создано это отправление
   * 
   * @return Order Объект заказа, для которого создано это отправление
   */
  public function getOrder()
  {
    return $this->order;
  }
  
  /**
   * Возвращает перечень товаров, содержащихся в отправлении
   * 
   * @return IShippable[] Перечень товаров, содержащихся в отправлении
   */
  public function getShippables()
  {
    return $this->shippables;
  }
  
  /**
   * Возвращает объявленную ценность отправления
   * 
   * @return float
   */
  public function getDeclaredValue()
  {
    $value = 0;
    
    $shippables = $this->getShippables();
    foreach ($shippables as $shippable)
    {
      $value += $shippable->getPrice()*$shippable->getQuantity();
    }
        
    return $value;
  }
}
