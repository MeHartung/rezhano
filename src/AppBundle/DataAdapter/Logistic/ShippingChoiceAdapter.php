<?php

namespace AppBundle\DataAdapter\Logistic;

use AccurateCommerce\Shipping\Estimate\ShippingEstimate;
use AccurateCommerce\Shipping\Method\ShippingMethod;
use AccurateCommerce\Shipping\Shipment\Shipment;
use AccurateCommerce\Shipping\ShippingManager;
use Accurateweb\ClientApplicationBundle\DataAdapter\ClientApplicationModelAdapterInterface;

class ShippingChoiceAdapter implements ClientApplicationModelAdapterInterface
{
  /**
   * @param $shippingMethod ShippingMethod
   * @return array
   */
  public function transform ($shippingMethod, $options = [])
  {
    if (!isset($options['shipment']) || !$options['shipment'] instanceof Shipment)
    {
      throw new \Exception('Required shipment');
    }

    if (!$shippingMethod->isAvailable($options['shipment']))
    {
      return array();
    }

    $estimate = $shippingMethod->estimate($options['shipment']);

    if (null === $estimate)
    {
      $estimate = new ShippingEstimate(null, null);
    }

    return array(
      'id' => $shippingMethod->getUid(),
      'uid' => $shippingMethod->getUid(),
      'shippingMethodName' => $shippingMethod->getName(),
      'clsid' => $shippingMethod->getClsid(),
      'name' => $shippingMethod->getName(),
      'cost' => $estimate->getCost(),
      'priority' => ShippingManager::getShippingMethodPriority($shippingMethod),
      'embeddedCalculatorCode' => null === $shippingMethod->getEmbeddedCalculatorCode() ? null : base64_encode($shippingMethod->getEmbeddedCalculatorCode()),
      'costString' => $estimate->formatCost(),
      'duration' => (float)$estimate->getDuration(),
      'durationString' => $estimate->getDurationString(),
      'help' => $shippingMethod->getHelp()
    );
  }

  public function getModelName ()
  {
    return 'ShippingChoice';
  }

  public function supports ($subject)
  {
    return $subject instanceof ShippingMethod;
  }
}
