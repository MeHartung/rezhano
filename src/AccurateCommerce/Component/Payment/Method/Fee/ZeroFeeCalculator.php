<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 25.06.2017
 * Time: 13:57
 */

namespace AccurateCommerce\Component\Payment\Method\Fee;


use StoreBundle\Entity\Store\Order\Order;

class ZeroFeeCalculator extends BaseFeeCalculator
{
  public function calculate(Order $order)
  {
    return 0;
  }
}