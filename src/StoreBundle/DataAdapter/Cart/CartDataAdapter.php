<?php

namespace StoreBundle\DataAdapter\Cart;


use AccurateCommerce\Component\Payment\Model\PaymentMethodManager;
use AccurateCommerce\Shipping\Method\ShippingMethodUserDefined;
use AccurateCommerce\Shipping\ShippingManager;
use Accurateweb\ClientApplicationBundle\DataAdapter\ClientApplicationModelAdapterInterface;
use Accurateweb\ClientApplicationBundle\DataAdapter\ClientApplicationModelTransformer;
use Doctrine\ORM\EntityManagerInterface;
use StoreBundle\DataAdapter\Logistic\ShippingMethodDataAdapter;
use StoreBundle\Entity\Store\Order\Order;
use StoreBundle\Entity\Store\Order\OrderItem;
use StoreBundle\Entity\Store\Shipping\ShippingMethod;
use StoreBundle\Util\DateFormatter;
use Symfony\Component\Validator\Constraints\DateTime;

class CartDataAdapter implements ClientApplicationModelAdapterInterface
{
  private $cartItemDataAdapter, $paymentMethodManager, $em, $shippingManager,
    $shippingMethodDataAdapter;
  
  public function __construct(CartItemDataAdapter $cartItemDataAdapter, PaymentMethodManager $paymentMethodManager,
                              EntityManagerInterface $em, ShippingManager $shippingManager,
                              ShippingMethodDataAdapter $shippingMethodDataAdapter)
  {
    $this->cartItemDataAdapter = $cartItemDataAdapter;
    $this->paymentMethodManager = $paymentMethodManager;
    $this->em = $em;
    $this->shippingManager = $shippingManager;
    $this->shippingMethodDataAdapter = $shippingMethodDataAdapter;
  }
  
  /**
   * @param $subject Order
   * @param array $options
   * @return array
   */
  public function transform($subject, $options = array())
  {
    $orderItems = [];
    
    foreach ($subject->getOrderItems() as $item)
    {
      /** @var $item OrderItem */
      array_push($orderItems, $this->cartItemDataAdapter->transform($item));
    }
    
    $preorder_date = '';
    
    if ($expected_delivery_date = $subject->getPreoderDate())
    {
      $preorder_date = sprintf('%s %s', DateFormatter::formatMonth($expected_delivery_date), $expected_delivery_date->format('Y'));
    }
    
    $shippingMethodsTransfrom = [];
    if ($subject->getShippingCityName() == 'Другой город')
    {
      $shippingMethods = $this->em->getRepository(ShippingMethod::class)->findNotCountryDelivery();
    } else
    {
      $shippingMethods = $this->em->getRepository(ShippingMethod::class)->findForRezhAndEkb();
    }
    $hasActiveMethod = false;
    #$shippingMethods = $this->shippingManager->getShippingMethods();
    /** @var ShippingMethod $shippingMethod */
    foreach ($shippingMethods as $shippingMethod)
    {
      if($subject->getPaymentMethod() !== null)
      {
        if($shippingMethod->getId() === $subject->getPaymentMethod()->getId())
        {
          $shippingMethod->setIsActive(true);
          $hasActiveMethod = true;
          # $_shippingMethod = $this->shippingManager->getShippingMethodByUid($shippingMethod->getUid());
        }
      }

      $shippingMethodsTransfrom[] = $this->shippingMethodDataAdapter->transform($shippingMethod, ['shipment' => $subject->getShipments()[0]]);
    }
    
    if(!$hasActiveMethod && count($shippingMethodsTransfrom)>0)
    {
      $result[0]['is_active'] = true;
    }
    /*foreach ($shippingMethods as $method)
    {
      $_method = $this->shippingManager->getShippingMethodByUid($method->getUid());
      
      $shippingMethodsTransfrom[] = $this->applicationModelTransformer->getClientModelCollectionData(
       $_method,
        'shipping.choice',
        ['shipment' => $_method ]
      );
    }*/
    
    return
      [
        'id' => $subject->getId(),
        'uid' => $subject->getUid(),
        'document_number' => $subject->getDocumentNumber(),
        'subtotal' => $subject->getSubtotal(),
        'shipping_cost' => $subject->getShippingCost(),
        'fee' => $subject->getFee(),
        'total' => $subject->getTotal(),
        'discount_sum' => $subject->getDiscountSum(),
        'discount_percentage' => $subject->getDiscountPercentage(),
        'customer_first_name' => $subject->getCustomerFirstName(),
        'customer_last_name' => $subject->getCustomerLastName(),
        'customer_email' => $subject->getCustomerEmail(),
        'customer_phone' => $subject->getCustomerPhone(),
        'customer_comment' => $subject->getCustomerPhone(),
        'shipping_city_name' => $subject->getShippingCityName(),
        'shipping_post_code' => $subject->getShippingPostCode(),
        'shipping_address' => $subject->getShippingAddress(),
        'shipping_method_uid' => $subject->getShippingMethod() ? $subject->getShippingMethod()->getUid() : null,
        'shipping_method_name' => $subject->getShippingMethod() ? $subject->getShippingMethod()->getName($subject->getShippingCityName()) : null,
        'payment_method_id' => $subject->getPaymentMethod() ? $subject->getPaymentMethod()->getId() : null,
        'payment_method_name' => $subject->getPaymentMethod() ? $subject->getPaymentMethod()->getName() : null,
        'status_id' => $subject->getOrderStatus() ? $subject->getOrderStatus()->getId() : null,
        'status_name' => $subject->getOrderStatus() ? $subject->getOrderStatus()->getName() : null,
        'created_at' => $subject->getCreatedAt(),
        'updated_at' => $subject->getUpdatedAt(),
        'user_id' => $subject->getUser() ? $subject->getUser()->getId() : null,
        'user_fio' => $subject->getUser() ? $subject->getUser()->getFio() : null,
        'order_items' => $orderItems,
        'is_paid' => !is_null($subject->getPaymentStatus()) ? $subject->getPaymentStatus()->isPaid() : null,
        'payment_status_name' => !is_null($subject->getPaymentStatus()) ? $subject->getPaymentStatus()->getName() : null,
        'preorder_date' => $preorder_date,
        'closest_pickup_date' => $subject->getClosestAvailablePickupDate()->format(\DateTime::ISO8601),
        'shipping_methods' => $shippingMethodsTransfrom
      ];
  }
  
  public function getModelName()
  {
    return 'Cart';
  }
  
  public function supports($subject)
  {
    return $subject instanceof Order;
  }
  
}