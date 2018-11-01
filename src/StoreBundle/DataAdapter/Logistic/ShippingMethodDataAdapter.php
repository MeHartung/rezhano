<?php

namespace StoreBundle\DataAdapter\Logistic;

use AccurateCommerce\Shipping\Estimate\ShippingEstimate;
use AccurateCommerce\Shipping\Method\ShippingMethod;
use AccurateCommerce\Shipping\Pickup\PickupPointInterface;
use AccurateCommerce\Shipping\Shipment\Shipment;
use AccurateCommerce\Shipping\ShippingManager;
use Accurateweb\ClientApplicationBundle\DataAdapter\ClientApplicationModelAdapterInterface;

class ShippingMethodDataAdapter implements ClientApplicationModelAdapterInterface
{
  private $shippingChoiceAdapter;
  private $shippingChoicePickupAdapter;

  public function __construct (ShippingChoiceAdapter $shippingChoiceAdapter, ShippingChoicePickupAdapter $shippingChoicePickupAdapter)
  {
    $this->shippingChoiceAdapter = $shippingChoiceAdapter;
    $this->shippingChoicePickupAdapter = $shippingChoicePickupAdapter;
  }

  /**
   * @param $shippingMethod \StoreBundle\Entity\Store\Shipping\ShippingMethod
   * @param $options array
   * @return array
   */
  public function transform ($shippingMethod, $options = [])
  {
    if (!isset($options['shipment']) || !$options['shipment'] instanceof Shipment)
    {
      throw new \Exception('Shipment required');
    }

/*    if (!empty($options['skip_deffered']) && $shippingMethod->getDeferredEstimate())
    {
      $choices = null;
    }
    else
    {
      $choices = $this->getShippingChoiceList($shippingMethod, $options['shipment']);
    }*/

    return array(
      'id' => $shippingMethod->getUid(),
      'name' => $shippingMethod->getName(),
      //'enabled' => $deliveryMethod->isApplicableTo(null, $this),
      //'details' => $deliveryMethod->getDetails($this),
      'options' => array('recipient_address_required' => $shippingMethod->getUid() != ShippingMethod::CLSID_PICKUP),
      'uid' => $shippingMethod->getUid(),
      'help' => $shippingMethod->getHelp(),
      'cost' => $shippingMethod->getCost(),
      'free_delivery_threshold' => $shippingMethod->getFreeDeliveryThreshold(),
      'priority' => ShippingManager::getShippingMethodPriority($shippingMethod),
      #'deferredEstimate' => $shippingMethod->getDeferredEstimate(),
      #'choices' => $choices
    );
  }

  public function getModelName ()
  {
    return 'ShippingMethod';
  }

  public function supports ($subject)
  {
    return $subject instanceof ShippingMethod;
  }
#ShippingMethod
  private function getShippingChoiceList ( $shippingMethod, Shipment $shipment)
  {
    $choices = array();

 /*   if ($shippingMethod->isAvailable($shipment))
    {*/
      $estimateCache = $shipment->getOrder()->getShippingEstimateCache();

      $choiceDefaults = array(
        'clsid' => $shippingMethod->getUid(),
        'priority' => ShippingManager::getShippingMethodPriority($shippingMethod),
        'embeddedCalculatorCode' => null === $shippingMethod->getEmbeddedCalculatorCode() ? null : base64_encode($shippingMethod->getEmbeddedCalculatorCode()),
      );

      #$estimate = $shippingMethod->estimate($shipment);
      $estimate = null;

      if ($shippingMethod->getUid() === ShippingMethod::CLSID_PICKUP)
      {
        $estimateCache[$shippingMethod->getUid()] = [];
        $pickupPoints = $shippingMethod->getPickupPoints($shipment);

        /** @var PickupPointInterface $pickupPoint */
        foreach ($pickupPoints as $i => $pickupPoint)
        {
          $choices[] = array_merge($choiceDefaults, $this->shippingChoicePickupAdapter->transform($shippingMethod, [
            'pickup_point' => $pickupPoint,
            'shipment' => $shipment
          ]));
        }
      }
      else
      {
        if (null === $estimate)
        {
          $estimate = new ShippingEstimate(null, null);
        }

        $estimateCache[$shippingMethod->getUid()] = $estimate;

        $choices[] = array_merge($choiceDefaults, $this->shippingChoiceAdapter->transform($shippingMethod, [
          'shipment' => $shipment
        ]));
      }

      $shipment->getOrder()->setShippingEstimateCache($estimateCache);
  /*  }*/

    return $choices;
  }
}
