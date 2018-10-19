<?php

namespace AppBundle\DataAdapter\Order;

use AppBundle\DataAdapter\Cart\CartDataAdapter;

class OrderDataAdapter extends CartDataAdapter
{
  public function transform ($subject, $options = array())
  {
    $data = parent::transform($subject, $options);

    $data['checkout_at'] = $subject->getCheckoutAt()?$subject->getCheckoutAt()->format('d.m.Y H:i'):null;

    return $data;
  }

}