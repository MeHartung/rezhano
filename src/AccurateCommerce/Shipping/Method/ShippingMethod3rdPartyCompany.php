<?php

namespace AccurateCommerce\Shipping\Method;

use AccurateCommerce\Shipping\Estimate\ShippingEstimate;
use AccurateCommerce\Shipping\Method\ShippingMethod;
use AccurateCommerce\Shipping\Shipment\Shipment;

/**
 * Класс способов доставки, предоставляемых сторонними транспортными компаниями.
 * 
 * На все такие способы распространяется бизнес-правило минимальной стоимости заказа для доставки
 *
 * @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
abstract class ShippingMethod3rdPartyCompany extends ShippingMethod
{
  const NOT_ENOUGH_DATA_MESSAGE = 'Недостаточно данных для расчета стоимости. Заказ содержит товары, для которых не указан вес или объем.';

  public function isAvailable(Shipment $shipment)
  {
    return parent::isAvailable($shipment);
  }

  /**
   * Выполняет оценку срока и стоимости доставки
   *
   * Для посылок, в которых есть хотя бы один товар, для которого не заданы вес или объем, стоимость доставки
   * автоматически сбрасывается в "уточнит оператор"
   *
   * @param Shipment $shipment
   * @return ShippingEstimate
   */
  public function estimate(Shipment $shipment)
  {
    $estimate = $this->doEstimate($shipment);

    return $estimate;
  }

  /**
   * Возвращает
   *
   * @param Shipment $shipment
   * @return ShippingEstimate
   */
  abstract protected function doEstimate(Shipment $shipment);
}
