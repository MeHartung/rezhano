<?php
/**
 * @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */

namespace AccurateCommerce\Shipping\Estimate;

use AccurateCommerce\Util\EndingFormatter;

/**
 * Хранит результат оценки срока и стоимости доставки
 */
class ShippingEstimate
{
  private $cost,
          $duration,
          $costEstimationError;

  /**
   * ShippingEstimate constructor.
   *
   * @param float|null $cost      Оценочная стоимость доставки
   * @param int|null   $duration  Оценочный срок доставки
   * @param string     $costEstimationError Замечания для расчета, которые потребуется сохранить в заказе
   */
  public function __construct($cost, $duration, $costEstimationError=null)
  {
    $this->cost = null !== $cost ? (int)ceil($cost) : null;
    $this->duration = $duration;
    $this->costEstimationError = $costEstimationError;
  }
  
  /**
   * Возвращает оценочную стоимость доставки отправления
   * 
   * @return float
   */
  public function getCost()
  {
    return $this->cost;
  }
  
  /**
   * Возвращает оценочную длительность доставки отправления
   * 
   * @return float
   */
  public function getDuration()
  {
    return $this->duration;
  }
  
  /**
   * 
   * @return string
   */
  public function getDurationString()
  {
    if (null === $this->duration)
    {
      return 'уточнит оператор';
    }
    
    if (is_numeric($this->duration))
    {
      return $this->duration.' '.EndingFormatter::format($this->duration, array('день', 'дня', 'дней'));
    }
    
    return (string)$this->duration;
  }
  
  /**
   * Возвращает форматированную стоимость доставки для отображения в виде строки
   * 
   * @return string
   */
  public function formatCost()
  {
    $cost = $this->getCost();
    
    if (null === $cost)
    {
      return 'уточнит оператор';
    }
    if (0 === $cost)
    {
      return 'бесплатно';
    }
    return $cost.' руб.';
  }

  /**
   * @return mixed
   */
  public function getCostEstimationError()
  {
    return $this->costEstimationError;
  }

  public function toArray()
  {
    return array('cost' => $this->getCost(), 'duration' => $this->getDuration(), 'costEstimationError' => $this->costEstimationError);
  }

  public function fromArray($arr)
  {
    if (isset($arr['cost'])) { $this->cost = $arr['cost']; }
    if (isset($arr['duration'])) { $this->duration = $arr['duration']; }
    if (isset($arr['costEstimationError'])) { $this->costEstimationError = $arr['costEstimationError']; }
  }
}
