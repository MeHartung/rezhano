<?php

namespace AccurateCommerce\Shipping\Method;

use AccurateCommerce\Shipping\Method\ShippingMethodInterface;
use AccurateCommerce\Shipping\Shipment\Shipment;

/**
 * Абстрактный класс выбираемого пользователем способа доставки.
 * 
 * Характеризует способ доставки, выбранный пользователем. Обеспечивает бизнес-логику заданного способа доставки.
 *
 * @author Dancy
 */
abstract class ShippingMethod implements ShippingMethodInterface
{
  const CLSID_SHIPPING = 'f40d5e67-7957-4506-ad6e-a2e88e871cde'; //Идентификатор класса способов доставки "До двери"
  const CLSID_PICKUP = '4d42fb65-8d6d-443b-88c5-8f6419028300';   //Идентификатор класса способов доставки "Получение в пункте выдачи"
          
  private $uid;
  private $name;
  private $clsid;
  private $embeddedCalculatorCode;
  private $internalName;
  protected $help = null;
  /**
   * Флаг отложенной оценки стоимости доставки.
   * 
   * Если установлен, этот флаг указывает на то, что расчет стоимости доставки займет достаточно много времени и 
   * по возможности должен быть выполнен асинхронно или на стороне клиента
   * 
   * @var boolean 
   */
  private $deferredEstimate;
  
  /**
   * Конструктор.
   * 
   * @param String $uid           Уникальный идентификатор способа доставки
   * @param String $clsid         Уникальный идентификатор класса способа доставки.
   * @param String $name          Отображаемое клиенту название способа доставки
   * @param String $internalName  Внутреннее название способа доставки - для операторов, администраторов, и т.д.
   */
  public function __construct($uid, $clsid, $name, $internalName = null)
  {
    $this->uid = $uid;
    $this->name = $name;
    $this->clsid = $clsid;
    $this->internalName = $internalName === null ? $this->name : $internalName;
    
    $this->setDeferredEstimate(false);
  }
  
  /**
   * Возвращает уникальный идентификатор способа доставки
   * 
   * @return String
   */
  public function getUid()
  {
    return $this->uid;
  }
  /** Добавлен параметер city для корректного вывода наименования способа доставки в админке */
  public function getName($city = null)
  {
    return $this->name;
  }
  
  public function getClsid()
  {
    return $this->clsid;
  }

  /**
   * Возвращает true, если способ доставки доступен для указанного отправления, иначе false
   * 
   * @param Shipment $shipment Отправление
   * @return boolean
   */
  public function isAvailable(Shipment $shipment)
  {
    $is_available = !$shipment->getOrder()->hasProductWithFreeDelivery();
    return $is_available;
  }
  
  /**
   * Возвращает true, если способ доставки предоставляет встраиваемый калькулятор доставки, в противном случае false
   * 
   * @return boolean
   */
  public function hasEmbeddedCalculator()
  {
    return null !== $this->embeddedCalculatorCode;
  }
  
  /**
   * Возвращает html-код встраиваемого калькулятора доставки
   * 
   * @return String
   */
  public function getEmbeddedCalculatorCode()
  {
    return $this->embeddedCalculatorCode;
  }
  
  protected function setEmbeddedCalculatorCode($v)
  {
    $this->embeddedCalculatorCode = $v;
  }
  
  public function getInternalName()
  {
    return $this->internalName;
  }
  
  /**
   * Устанавливает или снимает флаг отложенной оценки расчета срока и стоимости доставки.
   * 
   * @param bool $v
   */
  public function setDeferredEstimate($v)
  {
    $this->deferredEstimate = (bool)$v;
  }
  
  /**
   * Возвращает true, если способ доставки рекомендует отложенный расчет срока и стоимости доставки, 
   * в противном случае false
   * 
   * @return boolean
   */
  public function getDeferredEstimate()
  {
    return $this->deferredEstimate;
  }

  public function __toString()
  {
    return (string)$this->getName();
  }

  public function getHelp()
  {
    return $this->help;
  }
}
