<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace App\Payment\Method\Fee\Calculator;

use AccurateCommerce\Component\Payment\Method\Fee\BaseFeeCalculator;
use AccurateCommerce\Shipping\Method\Rupost\ShippingMethodRuPost;
use AccurateCommerce\Shipping\Method\ShippingMethod3rdPartyCompany;
use StoreBundle\Entity\Store\Order\Order;

class CashOnDeliveryPaymentMethodFeeCalculator extends BaseFeeCalculator
{
  const FEE_RUPOST = 0.06;
  const FEE_3RDPARTY = 0.03;

  public function calculate(Order $order)
  {
    $shippingMethod = $order->getShippingMethod();

    $fee = 0;

    if ($shippingMethod instanceof ShippingMethod3rdPartyCompany)
    {
      $fee = self::FEE_3RDPARTY;

      if ($shippingMethod instanceof ShippingMethodRuPost)
      {
        $fee = self::FEE_RUPOST;
      }
    }

    return $order->getSubtotal()*$fee;
  }

}