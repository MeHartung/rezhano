<?php

namespace StoreBundle\DataAdapter\Logistic;

use AccurateCommerce\Shipping\Estimate\ShippingEstimate;
use AccurateCommerce\Shipping\Method\ShippingMethod;
use AccurateCommerce\Shipping\Method\Store\ShippingMethodStoreCourier;
use AccurateCommerce\Shipping\Method\Store\ShippingMethodStorePickup;
use AccurateCommerce\Shipping\Pickup\PickupPointInterface;
use AccurateCommerce\Shipping\Shipment\Shipment;
use AccurateCommerce\Shipping\ShippingManager;
use Accurateweb\ClientApplicationBundle\DataAdapter\ClientApplicationModelAdapterInterface;
use StoreBundle\Service\Order\CartService;

class ShippingMethodDataAdapter implements ClientApplicationModelAdapterInterface
{
  private $shippingChoiceAdapter;
  private $shippingChoicePickupAdapter;
  private $cartService;

  public function __construct (ShippingChoiceAdapter $shippingChoiceAdapter,
                               ShippingChoicePickupAdapter $shippingChoicePickupAdapter, CartService $cartService)
  {
    $this->shippingChoiceAdapter = $shippingChoiceAdapter;
    $this->shippingChoicePickupAdapter = $shippingChoicePickupAdapter;
    $this->cartService = $cartService;
  }

  /**
   * @param $shippingMethod \StoreBundle\Entity\Store\Shipping\ShippingMethod
   * @param $options array
   * @return array
   */
  public function transform ($shippingMethod, $options = [])
  {/*
    if (!isset($options['shipment']) || !$options['shipment'] instanceof Shipment)
    {
      throw new \Exception('Shipment required');
    }*/

/*    if (!empty($options['skip_deffered']) && $shippingMethod->getDeferredEstimate())
    {
      $choices = null;
    }
    else
    {
      $choices = $this->getShippingChoiceList($shippingMethod, $options['shipment']);
    }*/
    $cart = $this->cartService->getCart();
    
    if($shippingMethod->getUid() === ShippingMethodStoreCourier::UID)
    {
      $deliveryCost = $cart->getSubtotal() >= 1000  ? 150.00 : 300.00;
      $shippingMethod->setCost($deliveryCost);
    }

    return array(
      'id' => $shippingMethod->getId(),
      'name' => $shippingMethod->getName(),
      //'enabled' => $deliveryMethod->isApplicableTo(null, $this),
      //'details' => $deliveryMethod->getDetails($this),
      'options' => array('recipient_address_required' => $shippingMethod->getUid() != ShippingMethodStorePickup::UID),
      'uid' => $shippingMethod->getUid(),
      'help' => $shippingMethod->getHelp(),
      'cost' => $shippingMethod->getCost(),
      'free_delivery_threshold' => $shippingMethod->getFreeDeliveryThreshold(),
      'priority' => ShippingManager::getShippingMethodPriority($shippingMethod),
      'is_active' => $shippingMethod->isActive(),
      'address' => $shippingMethod->getUid() ===  ShippingMethodStorePickup::UID ? $shippingMethod->getAddress() : null,
      'show_address' => $shippingMethod->getUid() === ShippingMethodStorePickup::UID ? $shippingMethod->getShowAddress() : null,
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
