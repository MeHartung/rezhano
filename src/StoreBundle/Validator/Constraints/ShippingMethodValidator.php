<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Validator\Constraints;

use AccurateCommerce\Shipping\Method\Store\ShippingMethodStoreCourier;
use AccurateCommerce\Shipping\ShippingManager;
use Doctrine\ORM\EntityManagerInterface;
use StoreBundle\Entity\Store\Order\Order;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ShippingMethodValidator extends ConstraintValidator
{
  private $shippingManager, $em;

  function __construct(ShippingManager $shippingManager, EntityManagerInterface $em)
  {
    $this->shippingManager = $shippingManager;
    $this->em = $em;
  }

  /**
   * Checks if the passed value is valid.
   *
   * @param Order $order The value that should be validated
   * @param Constraint $constraint The constraint for the validation
   */
  public function validate($order, Constraint $constraint)
  {
    if (!$order->getShippingMethod())
    {
      return;
    }

    $isValid = false;
    $shipments = $order->getShipments();

    $availableShippingMethods = $this->shippingManager->getAvailableShippingMethodsForShipment($shipments[0]);
    $allShippingMethods = $this->shippingManager->getShippingMethods();

    $orderShippingMethodUid = ($order->getShippingMethod() instanceof \StoreBundle\Entity\Store\Shipping\ShippingMethod)?
      $order->getShippingMethod()->getUid():
      $order->getShippingMethod();

    foreach ($availableShippingMethods as $shippingMethod)
    {
      if ($shippingMethod->getUid() === $orderShippingMethodUid)
      {
        $isValid = true;
        break;
      }
    }

    if (!$isValid)
    {
      $selectedShippingMethod = null;
      foreach ($allShippingMethods as $shippingMethod)
      {
        if ($shippingMethod->getUid() == $orderShippingMethodUid)
        {
          $selectedShippingMethod = $shippingMethod;
          break;
        }
      }

      $cityName = null;
      if($selectedShippingMethod->getUid() === ShippingMethodStoreCourier::UID)
      {
        $this->em->clear();
        $dbOrder = $this->em->getRepository(Order::class)->find($order->getId());
        $cityName = $dbOrder->getShippingCityName();
      }

      $this->context->buildViolation($constraint->message)
        ->atPath('shipping_method_id')
        ->setParameter('shipping_method_id', $selectedShippingMethod ? $selectedShippingMethod->getName($cityName) : $order->getShippingMethod())
        ->addViolation();
    }
  }
}