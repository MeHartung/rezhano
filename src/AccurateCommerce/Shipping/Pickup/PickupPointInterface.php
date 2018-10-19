<?php

namespace AccurateCommerce\Shipping\Pickup;

/**
 * Интерфейс пункта выдачи товаров. 
 * 
 * 
 * 
 * @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
interface PickupPointInterface
{
  function getAcceptedCreditCards();  
  
  /**
   * Возвращает адрес пункта выдачи
   * 
   * @return String
   */
  function getAddress();
  
  /**
   * Возвращает географические координаты пункта выдачи 
   * 
   * Координаты будут возвращены в виде массива из двух чисел с плавающей точкой, определяющих в порядке следования
   * широту и долготу пункта выдачи
   * 
   * @return float[]
   */
  function getGeoCoordinates();
  
  /**
   * Возвращает название пункта выдачи
   * 
   * @return String
   */
  function getName();
  
  /**
   * Возвращает расписание работы пункта выдачи в виде строки
   * 
   * @return String
   */
  function getTimetable();
  
  /**
   * Возвращает телефон пункта выдачи в виде строки
   * 
   * @return String
   */
  function getPhoneNumber();
  
  /**
   * Возвращает оценку срока и стоимости доставки отправления в пункт выдачи
   * 
   * @param Shipment $shipment
   * @return ShippingEstimate
   */
  function getShippingEstimate(Shipment $shipment);
}
