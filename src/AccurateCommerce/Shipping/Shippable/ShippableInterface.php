<?php

namespace AccurateCommerce\Shipping\Shippable;

/**
 * Интерфейс, который должны реализовывать все объекты, которые могут быть доставлены посетителю Интернет-магазина
 * 
 * @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
interface ShippableInterface
{
  /**
   * Возвращает вес единицы в кг
   * 
   * @return float
   */
  public function getWeight();
  
  /**
   * Возвращает объем единицы в м<sup>3</sup>
   * 
   * @return float
   */
  public function getVolume();
  
  /**
   * Возвращает количество единиц в отправлении
   * 
   * @return int
   */
  public function getQuantity();
  
  /**
   * @return IPurchasable
   */
  public function getPurchasable();
}
