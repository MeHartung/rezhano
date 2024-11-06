<?php

/*
 *  @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */

namespace AccurateCommerce\DataAdapter;

use AccurateCommerce\Shipping\Method\ShippingMethod;
use AccurateCommerce\Shipping\ShippingManager;

/**
 * 
 *
 * @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
class ShippingMethodClientModel implements ClientApplicationModelAdapterInterface
{
  /** @var ShippingMethod */
  private $shippingMethod;
  /**
   *
   * @var ShippingEstimate 
   */
  private $shippingChoices;
  
  public function __construct(ShippingMethod $shippingMethod, $shippingChoices = null)
  {
    $this->shippingMethod = $shippingMethod;
    $this->shippingChoices = $shippingChoices;
  }
  
  public function getClientModelId()
  {
    return $this->shippingMethod->getUid();
  }

  public function getClientModelName()
  {
    return 'ShippingMethod';
  }

  public function getClientModelValues($context = null)
  {    
    return array(
      'id' => $this->shippingMethod->getUid(),
      'name' => $this->shippingMethod->getName(),
      //'enabled' => $deliveryMethod->isApplicableTo(null, $this),
      //'details' => $deliveryMethod->getDetails($this),
      'options' => array('recipient_address_required' => $this->shippingMethod->getClsid() != ShippingMethod::CLSID_PICKUP),
      'clsid' => $this->shippingMethod->getClsid(),          
      //'help' => $deliveryMethod->getHelp(),
      'priority' => ShippingManager::getShippingMethodPriority($this->shippingMethod),
      'deferredEstimate' => $this->shippingMethod->getDeferredEstimate(),
      'choices' => $this->shippingChoices
    );        
  }

}
