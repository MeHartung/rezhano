<?php

namespace AccurateCommerce\Shipping\Pickup;

use AccurateCommerce\Shipping\Shipment\Shipment;

/**
 *
 * @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
interface PickupPointCollectionInterface
{
  /**
   * Возвращает список пунктов выдачи для заданного адреса
   * 
   * @param Shipment $shipment Отправление, для которого запрашивается список пунктов самовывоза
   * @return IPickupPoint[]
   */
  function getPickupPoints(Shipment $shipment);
}
