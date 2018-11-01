<?php

/*
 *  @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
namespace AccurateCommerce\DataAdapter;

use AccurateCommerce\Shipping\Estimate\ShippingEstimate;
use AccurateCommerce\Shipping\Method\ShippingMethod;
use AccurateCommerce\Shipping\Shipment\Shipment;
use AccurateCommerce\Shipping\ShippingManager;
use StoreBundle\Entity\Store\Order\Order;

/**
 * Класс отвечает за подготоку данных заказа для
 * передачи в клиентское приложение
 *
 * @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
class OrderClientApplicationAdapter
{
  private $order,
          $shippingManager;

  public function __construct(Order $order, ShippingManager $shippingManager)
  {
    $this->order = $order;
    $this->shippingManager = $shippingManager;
  }

  public function getCurrentShipments()
  {
    return $this->order->getShipments();
  }

  /**
   * Возвращает коллекцию клиентских моделей доступных для данного заказа
   * способов доставки
   *
   * @return ClientApplicationModelCollection
   */
  public function getShippingMethodClientModels()
  {
    $shipments = $this->getCurrentShipments();
    $shippingManager = $this->shippingManager;

    $shippingMethods = $shippingManager->getAvailableShippingMethodsForShipment($shipments[0]);
    
    $shippingMethodClientModels = new ClientApplicationModelCollection();
    foreach ($shippingMethods as $shippingMethod)
    {
  /*    if (!$shippingMethod->getDeferredEstimate())
      {
        $choices = $this->getShippingChoiceList($shippingMethod, $shipments[0]);
      }
      else
      {*/
        $choices = null;
     # }
      $shippingMethodClientModels->append(new ShippingMethodClientModel($shippingMethod, $choices));
    }

    return $shippingMethodClientModels;
  }

  /**
   * @param ShippingMethod $shippingMethod
   * @param Shipment $shipment
   * @return array
   */
  public function getShippingChoiceList( $shippingMethod, Shipment $shipment)
  {
    $choices = array();
  /*  if ($shippingMethod->isAvailable($shipment))
    {*/
      $estimateCache = $shipment->getOrder()->getShippingEstimateCache();

      $choiceDefaults = array(
          'clsid' => $shippingMethod->getUid(),
          'priority' => ShippingManager::getShippingMethodPriority($shippingMethod),
          'embeddedCalculatorCode' => null === $shippingMethod->getEmbeddedCalculatorCode() ? null : base64_encode($shippingMethod->getEmbeddedCalculatorCode()),
      );


      $estimate  = $shippingMethod->estimate($shipment);
//      if ($shippingMethod->getClsid() === ShippingMethod::CLSID_PICKUP)
//      {
//        $estimateCache[$shippingMethod->getUid()] = [];
//
//        $pickupPoints = $shippingMethod->getPickupPoints($shipment);
//        foreach ($pickupPoints as $i => $pickupPoint)
//        {
//          $estimate = $pickupPoint->getShippingEstimate($shipment);
//          if (null === $estimate)
//          {
//            $estimate = new ShippingEstimate(null, null);
//          }
//
//          $estimateCache[$shippingMethod->getUid()][$pickupPoint->getFullName()] = $estimate;
//
//          $photo = [];
//          if ($shippingMethod->getUid() === ShippingMethodSpecregionPickup::UID) {
//            $photoModels = $pickupPoint->getGallery('storePhoto')->getItems();
//            $photo = array(
//              ['thumb' => $photoModels[0]->getImageThumb('170x140')->web()->url(),
//                'image' => $photoModels[0]->getImageThumb('800x')->web()->url()]
//            );
//          }
//
//
//          $choices[] = array_merge($choiceDefaults, array(
//            'id' => $shippingMethod->getUid().'_'.$i,
//            'uid' => $shippingMethod->getUid(),
//            'name' => $pickupPoint->getName(),
//            'shippingMethodName' => $shippingMethod->getName(),
//            'cost' => $estimate->getCost(),
//            'costString' => $estimate->formatCost(),
//            'duration' => $estimate->getDuration(),
//            'durationString' => $estimate->getDurationString(),
//            'specregionDepartmentId' => ($shippingMethod->getUid() == ShippingMethodSpecregionPickup::UID) ? $pickupPoint->getId() : null,
//            'address' => $pickupPoint->getAddress(),
//            'timetable' => $pickupPoint->getTimetable(),
//            'phone' => PhoneNumberReplacer::replace($pickupPoint->getPhoneNumber()),
//            'acceptedCards' => $pickupPoint->getAcceptedCreditCards(),
//            'geocoordinates' => $pickupPoint->getGeoCoordinates(),
//            'photo' => $photo
//          ));
//        }
//      }
//      else
      {
        if (null === $estimate)
        {
          $estimate = new ShippingEstimate(null, null);
        }

        $estimateCache[$shippingMethod->getUid()] = $estimate;

        $choices[] = array_merge($choiceDefaults, array(
          'id' => $shippingMethod->getId(),
          'uid' => $shippingMethod->getUid(),
          'name' => $shippingMethod->getName(),
          'cost' => $estimate->getCost(),
          'costString' => $estimate->formatCost(),
          'duration' => $estimate->getDuration(),
          'durationString' => $estimate->getDurationString(),
          'help' => $shippingMethod->getHelp()
        ));
      }

      $shipment->getOrder()->setShippingEstimateCache($estimateCache);
   /* }*/

    return $choices;
  }
  
  public function setCurrentShipments($methods)
  {
  
  }
}