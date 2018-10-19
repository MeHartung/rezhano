<?php
/**
 * (c) 2017 ИП Рагозин Денис Николаевич. Все права защищены.
 *
 * Настоящий файл является частью программного продукта, разработанного ИП Рагозиным Денисом Николаевичем
 * (ОГРНИП 315668300000095, ИНН 660902635476).
 *
 * Алгоритм и исходные коды программного кода программного продукта являются коммерческой тайной
 * ИП Рагозина Денис Николаевича. Любое их использование без согласия ИП Рагозина Денис Николаевича рассматривается,
 * как нарушение его авторских прав.
 *
 * Ответственность за нарушение авторских прав наступает в соответствии с действующим законодательством РФ.
 */

namespace AccurateCommerce\Component\Checkout\Processor;


use AccurateCommerce\Component\Checkout\DocumentNumberGenerator;
use AccurateCommerce\Component\Checkout\Event\OrderCheckoutEvent;
use AccurateCommerce\Component\Payment\Model\PaymentMethodManager;
use AccurateCommerce\Shipping\Method\ShippingMethod;
use AccurateCommerce\Shipping\ShippingManager;
use Accurateweb\EmailTemplateBundle\Email\Factory\EmailFactory;
use Doctrine\ORM\EntityManager;
use StoreBundle\Entity\Store\Order\Order;
use StoreBundle\Entity\User\User;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CheckoutProcessor
{
  const EKB_ID_COURIER = 'eac20e0f-056a-4c10-9f43-7bee5c47167a';

  private $eventDispatcher;

  private $shippingManager;

  private $paymentMethodManager;

  private $documentNumberGenerator;

  private $entityManager;

  public function __construct(EventDispatcherInterface $eventDispatcher, ShippingManager $shippingManager, PaymentMethodManager $paymentMethodManager,
    DocumentNumberGenerator $documentNumberGenerator, EntityManager $entityManager)
  {
    $this->eventDispatcher = $eventDispatcher;
    $this->shippingManager = $shippingManager;
    $this->paymentMethodManager = $paymentMethodManager;
    $this->documentNumberGenerator = $documentNumberGenerator;
    $this->entityManager = $entityManager;
  }

  /**
   * @param Order $order
   * @param array $options
   * @return Order
   */
  public function process(Order $order, array $options = array())
  {
    $this->eventDispatcher->dispatch('store.order.checkout.pre', new OrderCheckoutEvent($order));

    $resolver = new OptionsResolver();
    $resolver->setDefaults(array(
      'preserve_calculations' => false,
      'isAdminEdit' =>false,
    ));

    $options = $resolver->resolve($options);

    if (!$options['preserve_calculations'])
    {
      $shipments = $order->getShipments();

      /** @var  $shippingMethod ShippingMethod*/

      $shippingMethod = $this->shippingManager->getShippingMethodByUid($order->getShippingMethodId());
      $shippingEstimate = $shippingMethod->estimate($shipments[0]);

      $order->setShippingCost($shippingEstimate ? $shippingEstimate->getCost() : null);
      $order->setShippingMethod($shippingMethod);
      $order->setFee($this->paymentMethodManager->calculateFee($order,
        $order->getPaymentMethod()));
    }

    $documentNumberGenerator = $this->documentNumberGenerator;
    $order->setDocumentNumber($documentNumberGenerator->generate());

    $order->setCheckoutStateId(Order::CHECKOUT_STATE_COMPLETE);
    $em = $this->entityManager;

    $em->persist($order);
    $em->flush();

    $this->eventDispatcher->dispatch('store.order.checkout', new OrderCheckoutEvent($order));

    return $order;
  }

}