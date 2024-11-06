<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 25.06.2017
 * Time: 13:53
 */

namespace AccurateCommerce\Component\Payment\Method\Fee;


use AccurateCommerce\Component\Core\IdentifiableInterface;
use AccurateCommerce\Component\Core\NamedObjectInterface;
use StoreBundle\Entity\Store\Order\Order;

interface FeeCalculatorInterface extends IdentifiableInterface, NamedObjectInterface
{
  public function calculate(Order $order);
}