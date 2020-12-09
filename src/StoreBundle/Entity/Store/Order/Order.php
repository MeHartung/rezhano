<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Entity\Store\Order;

use AccurateCommerce\DataAdapter\ClientApplicationModelAdapterInterface;
use AccurateCommerce\Shipping\Method\ShippingMethod;
use AccurateCommerce\Shipping\Method\ShippingMethodInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use AccurateCommerce\Shipping\Shipment\Address;
use AccurateCommerce\Shipping\Shipment\Shipment;
use AccurateCommerce\Util\UUID;
use StoreBundle\Entity\Integration\MoyskladQueue;
use StoreBundle\Entity\Notification\OrderNotification;
use StoreBundle\Entity\Store\Order\Status\OrderStatusHistory;
use StoreBundle\Entity\Store\Order\Status\OrderStatus;
use StoreBundle\Entity\Store\Payment\Method\PaymentMethod;
use StoreBundle\Entity\User\User;
use StoreBundle\Util\DateFormatter;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use JMS\Serializer\Annotation as JMS;
use StoreBundle\Validator\Constraints as StoreAssert;

/**
 * Заказ
 *
 * @package StoreBundle\Entity\Order
 *
 * @ORM\Table(name="orders")
 * @ORM\Entity(repositoryClass="StoreBundle\Repository\Store\Order\OrderRepository")
 * @ORM\HasLifecycleCallbacks()
 *
 * @JMS\ExclusionPolicy("all")
 *
 * @StoreAssert\PaymentMethod()
 * @StoreAssert\ShippingMethod()
 */
class Order implements ClientApplicationModelAdapterInterface
{
  use TimestampableEntity;

  const CHECKOUT_STATE_CART = 0x00; //Состояние заказа "Корзина"
  const CHECKOUT_STATE_DELIVERY = 0x01; //Выбор способа доставки
  const CHECKOUT_STATE_PAYMENT = 0x02; //Выбор способа оплаты
  const CHECKOUT_STATE_COMPLETE = 0x04; //Состояние заказа "Полностью оформлен"

  const CHECKOUT_STATE_CART_CHECKOUT = 0x09; //Оформленная корзина

  const CHECKOUT_STATE_NAME_COMPLETE = 'Заказ оформлен';
  
  const CUSTOMER_TYPE_INDIVIDUAL = 'individual'; // тип покупателя физ. лицо
  const CUSTOMER_TYPE_LEGAL = 'legal'; // тип покупателя юр. лицо

  /**
   * @var int
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue
   */
  private $id;

  /**
   * UID заказа для обмена с внешними системами (напр. 1С)
   *
   * @var string
   *
   * @ORM\Column(length=36)
   */
  private $uid;

  /**
   * Номер документа для покупателя
   *
   * @var string
   *
   * @ORM\Column(length=36, nullable=true)
   */
  private $documentNumber;

  /**
   * @var float
   *
   * @ORM\Column(type="decimal", scale=2)
   */
  private $subtotal;

  /**
   * @var float
   *
   * @ORM\Column(type="decimal", scale=2, nullable=true)
   */
  private $shippingCost;

  /**
   * @var float
   *
   * @ORM\Column(type="decimal", scale=2, nullable=true)
   */
  private $fee;

  /**
   * @var float
   *
   * @ORM\Column(type="decimal", scale=2)
   */
  private $total;

  /**
   * @var float
   *
   * @ORM\Column(type="decimal", scale=2)
   */
  private $discountSum = 0;


  /**
   * @var string;
   *
   * @ORM\Column(length=255, nullable=true)
   */
  private $customerFirstName;

  /**
   * @var string
   *
   * @ORM\Column(length=255, nullable=true)
   */
  private $customerLastName;

  /**
   * @var string
   *
   * @ORM\Column(length=32, nullable=true)
   */
  private $customerPhone;

  /**
   * @var string
   *
   * @ORM\Column(length=255, nullable=true)
   */
  private $customerEmail;

  /**
   * @var string
   *
   * @ORM\Column(length=1024, nullable=true)
   */
  private $customerComment;

  /**
   * @var string
   *
   * @ORM\Column(length=36, nullable=true)
   */
  private $shippingCityFiasAouid;

  /**
   * @var string
   *
   * @ORM\Column(length=255, nullable=true)
   */
  private $shippingCityName;

  /**
   * @var integer
   *
   * @ORM\Column(type="integer", nullable=true)
   */
  private $shippingPostCode;

  /**
   * @var string
   *
   * @ORM\Column(length=255, nullable=true)
   */
  private $shippingAddress;
  
  /**
   * @var \StoreBundle\Entity\Store\Shipping\ShippingMethod
   * @ORM\ManyToOne(targetEntity="StoreBundle\Entity\Store\Shipping\ShippingMethod")
   */
  private $shippingMethod;

  /**
   * @var \DateTime|null
   * @ORM\Column(type="datetime", nullable=true)
   */
  private $shippingDate;

  /**
   * @var PaymentMethod
   *
   * @ORM\ManyToOne(targetEntity="StoreBundle\Entity\Store\Payment\Method\PaymentMethod", cascade={"persist"})
   *
   */
  private $paymentMethod;

  /**
   * @var ArrayCollection
   * @JMS\Expose
   * @ORM\OneToMany(targetEntity="StoreBundle\Entity\Store\Order\OrderItem", mappedBy="order", cascade={"persist", "remove"})
   */
  private $orderItems;

  /**
   * @var int
   *
   * @ORM\Column(type="integer")
   */
  private $checkoutStateId;

  /**
   * @var OrderStatus
   *
   * @ORM\ManyToOne(targetEntity="StoreBundle\Entity\Store\Order\Status\OrderStatus")
   */
  private $orderStatus;

  /**
   * @var ArrayCollection
   * @ORM\OneToMany(targetEntity="StoreBundle\Entity\Store\Order\Status\OrderStatusHistory", mappedBy="order")
   */
  private $orderStatusHistory;

  private $orderAdminStatus;

  /**
   * Предполагаемая дата предзаказа
   * @var \DateTime
   * @ORM\Column(type="date", nullable=true)
   */
  private $preoder_date;

  /**
   * @var User
   * @ORM\ManyToOne(targetEntity="StoreBundle\Entity\User\User", inversedBy="orders", cascade={"persist"})
   */
  private $user;

  /**
   * @var int
   *
   * @ORM\Column(type="integer", nullable=true)
   */
  private $virtuemartOrderId;

  private $has_product_free_delivery = false;

  private $shipments,
    $shippingEstimateCache = array();
  
  /**
   * @var string
   *
   * @ORM\ManyToOne(targetEntity="StoreBundle\Entity\Store\Order\PaymentStatus\OrderPaymentStatus", cascade={"persist", "remove"})
   */
  private $paymentStatus;

  /**
   * @var \DateTime|null
   * @ORM\Column(type="datetime", nullable=true)
   */
  private $checkoutAt;
  
  /**
   * @var boolean
   * @ORM\Column(name="moysklad_sent", type="boolean", options={"default"=false}, nullable=true)
   */
  private $moyskladSent=false;
  
  /**
   * @var MoyskladQueue
   * @ORM\OneToOne(targetEntity="StoreBundle\Entity\Integration\MoyskladQueue", mappedBy="order")
   */
  private $moysklad_queue;
  
  /**
   * @var string
   * @ORM\Column(type="string", options={"default": "individual"})
   */
  private $customerType = self::CUSTOMER_TYPE_INDIVIDUAL;

  /**
   * @var OrderNotification[]|ArrayCollection
   * @ORM\OneToMany(targetEntity="StoreBundle\Entity\Notification\OrderNotification",mappedBy="order",orphanRemoval=true)
   */
  private $notifications;
  
  /**
   * Конструктор.
   */
  public function __construct()
  {
    $this->orderItems = new ArrayCollection();
    $this->uid = $this->generateUid();
    $this->checkoutStateId = self::CHECKOUT_STATE_CART;
    $this->orderStatusHistory = new ArrayCollection();
    $this->notifications = new ArrayCollection();

    $this->discountSum = 0;
  }

  /**
   * @return int
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @param int $id
   * @return Order
   */
  public function setId($id)
  {
    $this->id = $id;

    return $this;
  }

  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }

  /**
   * @param string $uid
   * @return Order
   */
  public function setUid($uid)
  {
    $this->uid = $uid;

    return $this;
  }

  /**
   * @return string
   */
  public function getDocumentNumber()
  {
    return $this->documentNumber;
  }

  /**
   * @param string $documentNumber
   * @return Order
   */
  public function setDocumentNumber($documentNumber)
  {
    $this->documentNumber = $documentNumber;

    return $this;
  }

  /**
   * @return ArrayCollection|OrderItem[]
   */
  public function getOrderItems()
  {
    return $this->orderItems;
  }

  /**
   * @param ArrayCollection $orderItems
   * @return Order
   */
  public function setOrderItems($orderItems)
  {
    $this->orderItems = $orderItems;

    return $this;
  }

  /**
   * Добавляет товар к заказу
   * @param OrderItem $item
   * @return Order
   */
  public function addOrderItem(OrderItem $item)
  {
    /** @var $item OrderItem */
    $this->orderItems->add($item);
    $item->setOrder($this);
    return $this;
  }

  public function getOrderItem($purchasableId)
  {
    foreach ($this->getOrderItems() as $orderItem)
    {
      if ($orderItem->getPurchasableId() == $purchasableId)
      {
        return $orderItem;
      }
    }

    return null;
  }

  public function generateUid()
  {
    return UUID::mint(4);
  }

  /**
   * @param float $total
   */
  public function setTotal($total)
  {
    $this->total = $total;
  }

  /**
   * @param float $subtotal
   */
  public function setSubtotal($subtotal)
  {
    $this->subtotal = $subtotal;
  }

  /**
   * @return int
   */
  public function getCheckoutStateId()
  {
    return $this->checkoutStateId;
  }

  /**
   * @param int $checkoutStateId
   * @return Order
   */
  public function setCheckoutStateId($checkoutStateId)
  {
    $this->checkoutStateId = $checkoutStateId;
    return $this;
  }

  /**
   * @return string
   */
  public function getCustomerComment()
  {
    return $this->customerComment;
  }

  /**
   * @param string $customerComment
   */
  public function setCustomerComment($customerComment)
  {
    $this->customerComment = $customerComment;
  }

  /**
   * @return string
   */
  public function getCustomerPhone()
  {
    return $this->customerPhone;
  }

  /**
   * @param string $customerPhone
   */
  public function setCustomerPhone($customerPhone)
  {
    $this->customerPhone = $customerPhone;
  }

  /**
   * Создает коллекцию отправлений для заказа
   *
   * @return Shipment[]
   */
  protected function createShipments()
  {

    $destinationCityName = $this->getShippingCityName();
    $destinationCityAoguid = $this->getShippingCityFiasAouid();

    return array(new Shipment($this, $this->getOrderItems(), new Address(620000, 'Екатеринбург', '2763c110-cb8b-416a-9dac-ad28a55b4402', null),
      new Address($this->getShippingPostCode(), $destinationCityName, $destinationCityAoguid, null)));
  }


  /**
   * Возвращает набор посылок, которые требуется передать клиенту, чтобы выполнить заказ
   *
   * @return Shipment[]
   */
  public function getShipments()
  {
    if (null === $this->shipments)
    {
      $this->shipments = $this->createShipments();
    }

    return $this->shipments;
  }

  /**
   * @return string
   */
  public function getShippingCityFiasAouid()
  {
    return $this->shippingCityFiasAouid;
  }

  /**
   * @param string $shippingCityFiasAouid
   * @return Order
   */
  public function setShippingCityFiasAouid($shippingCityFiasAouid)
  {
    $this->shippingCityFiasAouid = $shippingCityFiasAouid;

    return $this;
  }

  /**
   * @return string
   */
  public function getShippingCityName()
  {
    return $this->shippingCityName;
  }

  /**
   * @param string $shippingCityName
   *
   * @return Order
   */
  public function setShippingCityName($shippingCityName)
  {
    $this->shippingCityName = $shippingCityName;

    return $this;
  }

  /**
   * @return array
   */
  public function getShippingEstimateCache()
  {
    return $this->shippingEstimateCache;
  }

  /**
   * @param array $shippingEstimateCache
   * @return Order
   */
  public function setShippingEstimateCache($shippingEstimateCache)
  {
    $this->shippingEstimateCache = $shippingEstimateCache;

    return $this;
  }

  /**
   * @return string
   */
  public function getCustomerFirstName()
  {
    return $this->customerFirstName;
  }

  /**
   * @param string $customerFirstName
   * @return Order
   */
  public function setCustomerFirstName($customerFirstName)
  {
    $this->customerFirstName = $customerFirstName;

    return $this;
  }

  /**
   * Возвращает полное имя заказчика
   *
   * @return string
   */
  public function getCustomerFullName()
  {
    $nameParts = [];
    if ($this->customerFirstName)
    {
      $nameParts[] = $this->customerFirstName;
    }

    if ($this->customerLastName)
    {
      $nameParts[] = $this->customerLastName;
    }

    return implode(' ', $nameParts);
  }

  /**
   * @return string
   */
  public function getCustomerLastName()
  {
    return $this->customerLastName;
  }

  /**
   * @param string $customerLastName
   * @return Order
   */
  public function setCustomerLastName($customerLastName)
  {
    $this->customerLastName = $customerLastName;

    return $this;
  }

  /**
   * @return int
   */
  public function getShippingPostCode()
  {
    return $this->shippingPostCode;
  }

  /**
   * @param int $shippingPostCode
   * @return Order
   */
  public function setShippingPostCode($shippingPostCode)
  {
    $this->shippingPostCode = $shippingPostCode;

    return $this;
  }

  /**
   * @return string
   */
  public function getShippingAddress()
  {
    return $this->shippingAddress;
  }

  /**
   * @param string $shippingAddress
   * @return Order
   */
  public function setShippingAddress($shippingAddress)
  {
    $this->shippingAddress = $shippingAddress;

    return $this;
  }

  /**
   * @return \StoreBundle\Entity\Store\Shipping\ShippingMethod
   */
  public function getShippingMethod()
  {
    return $this->shippingMethod;
  }

  /**
   * @param ShippingMethod $shippingMethod
   */
  public function setShippingMethod($shippingMethod)
  {
    $this->shippingMethod = $shippingMethod;
  }

  /**
   * @return PaymentMethod
   */
  public function getPaymentMethod()
  {
    return $this->paymentMethod;
  }

  /**
   * @param PaymentMethod $paymentMethod
   * @return Order
   */
  public function setPaymentMethod($paymentMethod)
  {
    $this->paymentMethod = $paymentMethod;

    return $this;
  }

  /**
   * @return float
   */
  public function getSubtotal()
  {
    return $this->subtotal;
  }

  /**
   * @return float
   */
  public function getShippingCost()
  {
    return $this->shippingCost;
  }

  public function setShippingCost($shippingCost)
  {
    if (null === $shippingCost)
    {
      $this->shippingCost = null;
    } else
    {
      $this->shippingCost = (float)$shippingCost;
    }

    return $this;
  }

  /**
   * @return float
   */
  public function getTotal()
  {
    return $this->total;
  }

  /**
   * @return float
   */
  public function getFee()
  {
    return $this->fee;
  }

  /**
   * @param float $fee
   * @return Order
   */
  public function setFee($fee)
  {
    if (null === $fee)
    {
      $this->fee = $fee;
    } else
    {
      $this->fee = (float)$fee;
    }

    return $this;
  }

  /**
   * @return string
   */
  public function getCustomerEmail()
  {
    return $this->customerEmail;
  }

  /**
   * @param string $customerEmail
   * @return Order
   */
  public function setCustomerEmail($customerEmail)
  {
    $this->customerEmail = $customerEmail;

    return $this;
  }

  /**
   * Возвращает ID заказа в VirtueMart
   *
   * @return int
   */
  public function getVirtuemartOrderId()
  {
    return $this->virtuemartOrderId;
  }

  /**
   * Задает ID заказа в VirtueMart
   *
   * @param int $virtuemartOrderId
   */
  public function setVirtuemartOrderId($virtuemartOrderId)
  {
    $this->virtuemartOrderId = $virtuemartOrderId;
  }

  public static function getCheckoutStateNames()
  {
    return [
      self::CHECKOUT_STATE_COMPLETE => self::CHECKOUT_STATE_NAME_COMPLETE,
    ];
  }

  public function getCheckoutStateName()
  {
    $map = self::getCheckoutStateNames();
    return isset($map[$this->checkoutStateId]) ? $map[$this->checkoutStateId] : null;
  }

  function __toString()
  {
    return (string)$this->getDocumentNumber();
  }

  public function getFullShippingAddress()
  {
    $addressParts = array();
    if ($this->getShippingPostCode())
    {
      $addressParts[] = $this->getShippingPostCode();
    }
    if ($this->getShippingCityName())
    {
      $addressParts[] = $this->getShippingCityName();
    }
    if ($this->getShippingAddress())
    {
      $addressParts[] = $this->getShippingAddress();
    }

    return implode(', ', $addressParts);
  }

  /**
   * Устанавливаем имеется ли у данного заказа товары с бесплатной доставкой
   * @ORM\PostLoad()
   */
  public function setHasProductWithFreeDelivery()
  {
    /** @var OrderItem[] $order_items */
    $order_items = $this->getOrderItems();

    if ($order_items)
    {
      foreach ($order_items as $order_item)
      {
        $product = $order_item->getProduct();

        if ($product && $product->isFreeDelivery())
        {
          $this->has_product_free_delivery = true;
          return;
        }
      }
    }

    $this->has_product_free_delivery = false;
  }

  /**
   * @return OrderStatus
   */
  public function getOrderStatus()
  {
    return $this->orderStatus;
  }

  /**
   * @param OrderStatus $orderStatus
   */
  public function setOrderStatus($orderStatus)
  {
    $this->orderStatus = $orderStatus;
  }

  /**
   * @return ArrayCollection
   */
  public function getOrderStatusHistory()
  {
    $criteria = Criteria::create();
    $criteria->orderBy(['createdAt' => 'DESC']);

    return $this->orderStatusHistory->matching($criteria);
  }

  /**
   * @param ArrayCollection $orderStatusHistory
   */
  public function setOrderStatusHistory($orderStatusHistory)
  {
    $this->orderStatusHistory = $orderStatusHistory;
  }

  /**
   * Т.к не совсем понятно что может вернуть last(),
   * то упорядочим коллекцию по дате создания
   * и вернём последнюю запись из журнала
   *
   * @return OrderStatusHistory|null
   */
  public function getLastOrderStatusHistory()
  {
    $criteria = Criteria::create()
      ->orderBy(array("createdAt" => Criteria::DESC));

    return $this->getOrderStatusHistory()->matching($criteria)->first();
  }

  /**
   * Чтобы не ломать логику работы getOrderStatus,
   * сделаем геттер для админки.
   * Нужен только для чтения.
   * @return string
   */
  public function getOrderAdminStatus()
  {
    if ($orderStatus = $this->getOrderStatus())
    {
      $orderStatusName = $orderStatus->getName();
      $orderStatusReason = !$this->getLastOrderStatusHistory() ? ''
                                   : "\n" . $this->getLastOrderStatusHistory()->getReason();

    return sprintf('%s %s', $orderStatusName, $orderStatusReason );
    }

    if ($expected_delivery_date = $this->getPreoderDate())
    {
      $date = sprintf('%s %s', DateFormatter::formatMonth($expected_delivery_date), $expected_delivery_date->format('Y'));
      return sprintf('Ожидается: %s', $date);
    }

    return '';
  }

  /**
   * @return User
   */
  public function getUser()
  {
    return $this->user;
  }

  /**
   * @param User $user
   * @return Order
   */
  public function setUser($user)
  {
    $this->user = $user;
    return $this;
  }

  /**
   * @return boolean
   */
  public function hasProductWithFreeDelivery()
  {
    return $this->has_product_free_delivery;
  }

  public function toJson()
  {
    $orderItems = [];

    foreach ($this->getOrderItems() as $item)
    {
      /** @var $item OrderItem */
      array_push($orderItems, $item->toJSON());
    }

    $preorder_date = '';

    if ($expected_delivery_date = $this->getPreoderDate())
    {
      $preorder_date = sprintf('%s %s', DateFormatter::formatMonth($expected_delivery_date), $expected_delivery_date->format('Y'));
    }

    return
      [
        'id' => $this->getId(),
        'uid' => $this->getUid(),
        'document_number' => $this->getDocumentNumber(),
        'subtotal' => $this->getSubtotal(),
        'shipping_cost' => $this->getShippingCost(),
        'fee' => $this->getFee(),
        'total' => $this->getTotal(),
        'discount_sum' => $this->getDiscountSum(),
        'discount_percentage' => $this->getDiscountPercentage(),
        'customer_first_name' => $this->getCustomerFirstName(),
        'customer_last_name' => $this->getCustomerLastName(),
        'customer_email' => $this->getCustomerEmail(),
        'customer_phone' => $this->getCustomerPhone(),
        'customer_comment' => $this->getCustomerPhone(),
        'shipping_city_name' => $this->getShippingCityName(),
        'shipping_post_code' => $this->getShippingPostCode(),
        'shipping_address' => $this->getShippingAddress(),
        'shipping_method_uid' => $this->getShippingMethod()?$this->getShippingMethod()->getUid():null,
        'shipping_method_name' => $this->getShippingMethod()?$this->getShippingMethod()->getName($this->getShippingCityName()):null,
        'payment_method_id' => $this->getPaymentMethod()?$this->getPaymentMethod()->getId():null,
        'payment_method_name' => $this->getPaymentMethod()?$this->getPaymentMethod()->getName():null,
        'status_id' => $this->getOrderStatus() ? $this->getOrderStatus()->getId() : null,
        'status_name' => $this->getOrderStatus() ? $this->getOrderStatus()->getName() : null,
        'created_at' => $this->getCreatedAt(),
        'updated_at' => $this->getUpdatedAt() ,
        'user_id' => $this->getUser() ? $this->getUser()->getId() : null,
        'user_fio' => $this->getUser() ? $this->getUser()->getFio() : null,
        'order_items' => $orderItems,
        'is_paid' => !is_null($this->getPaymentStatus()) ? $this->getPaymentStatus()->isPaid() : null,
        'payment_status_name' => !is_null($this->getPaymentStatus()) ? $this->getPaymentStatus()->getName() : null,
        'preorder_date' => $preorder_date
      ];
  }

  public function getClientModelName()
  {
    return 'Order';
  }

  public function getClientModelValues($context = null)
  {
    return $this->toJson();
  }

  public function getClientModelId()
  {
    return $this->getId();
  }

  /**
   * @return string
   */
  public function getPaymentStatus()
  {
    return $this->paymentStatus;
  }

  /**
   * @param string $paymentStatus
   */
  public function setPaymentStatus($paymentStatus)
  {
    $this->paymentStatus = $paymentStatus;
  }

  /**
   * @return float
   */
  public function getDiscountSum()
  {
    return $this->discountSum;
  }

  /**
   * @param float $discountSum
   */
  public function setDiscountSum(float $discountSum)
  {
    $this->discountSum = $discountSum;
  }

  public function getDiscountPercentage()
  {
    return abs(((int)$this->getDiscountSum() == 0) ? 0 :
      round($this->getDiscountSum()/(($this->getSubtotal() + $this->getDiscountSum())/100),0));
  }

  /**
   * @return \DateTime
   */
  public function getPreoderDate ()
  {
    return $this->preoder_date;
  }

  /**
   * @param \DateTime $preoder_date
   * @return $this
   */
  public function setPreoderDate ($preoder_date)
  {
    $this->preoder_date = $preoder_date;
    return $this;
  }

  /**
   * @return \DateTime|null
   */
  public function getShippingDate ()
  {
    return $this->shippingDate;
  }

  /**
   * @param \DateTime|null $shippingDate
   * @return $this
   */
  public function setShippingDate (\DateTime $shippingDate=null)
  {
    $this->shippingDate = $shippingDate;
    return $this;
  }

  /**
   * @return \DateTime|null
   */
  public function getCheckoutAt ()
  {
    return $this->checkoutAt;
  }

  /**
   * @param \DateTime|null $checkoutAt
   * @return $this
   */
  public function setCheckoutAt (\DateTime $checkoutAt=null)
  {
    $this->checkoutAt = $checkoutAt;
    return $this;
  }

  /**
   * Возвращает ближайшую доступную дату получения заказа самовывозом
   */
  public function getClosestAvailablePickupDate()
  {
    return new \DateTime("+5 days");
  }
  
  /**
   * @return bool
   */
  public function isMoyskladSent(): bool
  {
    return $this->moyskladSent;
  }
  
  /**
   * @param bool $moyskladSent
   */
  public function setMoyskladSent(bool $moyskladSent): void
  {
    $this->moyskladSent = $moyskladSent;
  }
  
  /**
   * @return MoyskladQueue
   */
  public function getMoyskladQueue(): ?MoyskladQueue
  {
    return $this->moysklad_queue;
  }
  
  /**
   * @param MoyskladQueue $moysklad_queue
   */
  public function setMoyskladQueue(?MoyskladQueue $moysklad_queue): void
  {
    $this->moysklad_queue = $moysklad_queue;
  }
  
  /**
   * @return string
   */
  public function getCustomerType(): ?string
  {
    return $this->customerType;
  }
  
  /**
   * @param string $customerType
   */
  public function setCustomerType(?string $customerType): void
  {
    $this->customerType = $customerType;
  }
}