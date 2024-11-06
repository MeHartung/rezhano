<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace AccurateCommerce\Component\Payment\Model;

use AccurateCommerce\Component\Payment\Method\Availability\AvailabilityDecisionManager;
use AccurateCommerce\Component\Payment\Method\Fee\FeeCalculatorInterface;
use AccurateCommerce\Component\Payment\Method\Fee\FeeCalculatorRepository;
use StoreBundle\Entity\Store\Order\Order;
use StoreBundle\Repository\Store\Payment\Method\PaymentMethodRepository;

class PaymentMethodManager
{
  /**
   * @var AvailabilityDecisionManager[]
   */
  private $decisionManagers;

  /**
   * @var PaymentMethodRepository
   */
  private $repository;

  /**
   * @var FeeCalculatorRepository
   */
  private $feeCalculatorRepository;

  public function __construct(PaymentMethodRepository $repository, FeeCalculatorRepository $feeCalculatorRepository)
  {
    $this->decisionManagers = [];
    $this->repository = $repository;
    $this->feeCalculatorRepository = $feeCalculatorRepository;
  }

  public function addAvailabilityDecisionManager(AvailabilityDecisionManager $decisionManager)
  {
    $this->decisionManagers[$decisionManager->getId()] = $decisionManager;
  }

  protected function getAvailabilityDecisionManager(PaymentMethod $paymentMethod)
  {
    $decisionManagerId = $paymentMethod->getAvailabilityDecisionManagerId();

    return isset($this->decisionManagers[$decisionManagerId]) ? $this->decisionManagers[$decisionManagerId] : null;
  }

  public function isAvailable(PaymentMethod $paymentMethod, Order $order)
  {
    $decisionManager = $this->getAvailabilityDecisionManager($paymentMethod);

    return $decisionManager->decide($order, $paymentMethod);
  }

  /**
   * @param Order $order
   * @return PaymentMethod[]
   */
  public function getAvailablePaymentMethods(Order $order)
  {
    $paymentMethods = $this->repository->findBy(['enabled' => true], ['position' => 'asc']);

    $availablePaymentMethods = [];

    foreach ($paymentMethods as $paymentMethod)
    {
      if ($this->isAvailable($paymentMethod, $order))
      {
        $availablePaymentMethods[] = $paymentMethod;
      }
    }

    return $availablePaymentMethods;
  }

  /**
   * @return AvailabilityDecisionManager[]
   */
  public function getAvailabilityDecisionManagers()
  {
    return $this->decisionManagers;
  }

  /**
   * @param PaymentMethod $paymentMethod
   * @return FeeCalculatorInterface
   */
  public function getFeeCalculator(PaymentMethod $paymentMethod)
  {
    return $this->feeCalculatorRepository->find($paymentMethod->getFeeCalculatorId());
  }

  public function calculateFee(Order $order, PaymentMethod $paymentMethod)
  {
    return $this->getFeeCalculator($paymentMethod)->calculate($order);
  }
}