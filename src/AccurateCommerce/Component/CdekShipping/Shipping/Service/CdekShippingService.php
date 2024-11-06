<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace AccurateCommerce\Component\CdekShipping\Shipping\Service;

use AccurateCommerce\Component\CdekShipping\Api\CdekApiClient;
use AccurateCommerce\Component\CdekShipping\Shipping\Method\ShippingMethodCdekCourier;
use AccurateCommerce\Component\CdekShipping\Shipping\Method\ShippingMethodCdekTerminal;
use AccurateCommerce\Shipping\Service\ShippingService;

/**
 * Cdek Shipping Service
 *
 * @package AccurateCommerce\Component\CdekShipping\Shipping\Service
 */
class CdekShippingService extends ShippingService
{
  private $api;

  private $courierTariffId;

  private $pickupTariffId;

  public function __construct($uid, CdekApiClient $api, $courierTariffId, $pickupTariffId)
  {
    $this->api = $api;

    $this->courierTariffId = $courierTariffId;
    $this->pickupTariffId = $pickupTariffId;

    parent::__construct($uid);
  }

  protected function configure()
  {
    $this->addShippingMethod(new ShippingMethodCdekCourier($this->api, $this->courierTariffId));
    $this->addShippingMethod(new ShippingMethodCdekTerminal($this->api, $this->pickupTariffId));
  }
}