<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace AccurateCommerce\Component\CdekShipping\Api;


use AccurateCommerce\Shipping\Estimate\ShippingEstimate;
use AccurateCommerce\Util\EndingFormatter;

class CdekShippingInfo
{
  private $info,
    $estimate,
    $pickupEstimate,
    $pickupPoints;

  /**
   * Конструктор.
   *
   * @param array $data Ответ API "Деловых Линий"
   */
  public function __construct(ShippingEstimate $shippingEstimate, ShippingEstimate $pickupEstimate=null, $pickupPoints=array(), $data=null)
  {
    $this->info = $data;

    $this->estimate = $shippingEstimate;
    $this->pickupPoints = $pickupPoints;
    $this->pickupEstimate = $pickupEstimate;
  }

  public static function fromApiResponse($data)
  {
    //$data = json_decode($data, true);

    $duration = null;
    $duration_min = $data['deliveryPeriodMin'];
    $duration_max = $data['deliveryPeriodMax'];

    if ($duration_min == $duration_max)
    {
      $duration = sprintf('%d %s', $duration_min, EndingFormatter::format($duration_min, array('день', 'дня', 'дней')));
    }
    else
    {
      $duration = sprintf("от %d до %d дней", $duration_min, $duration_max);
    }

    $estimate = new ShippingEstimate($data['price'] > 0 ? $data['price'] : null, $duration);

    return new CdekShippingInfo($estimate, $estimate, [], $data);
  }

  /**
   * Возвращает оценку срока и стоимости доставки для доставки курьером
   *
   * @return ShippingEstimate Оценка срока и стоимости доставки для курьерской доставки
   */
  public function getShippingEstimate()
  {
    return $this->estimate;
  }

  /**
   * Возвращает оценку срока и стоимости доставки для доставки до терминала
   *
   * @return ShippingEstimate Оценка срока и стоимости доставки для доставки до терминала
   */
  public function getPickupEstimate()
  {
    return $this->pickupEstimate;
  }

  public function getPickupPoints()
  {
    return $this->pickupPoints;
  }

  public function getAll()
  {
    return $this->info;
  }

  public function isPickupAvailable()
  {
    return !empty($this->pickupPoints);
  }

}