<?php

namespace StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Accurateweb\SettingBundle\Model\Entity\Setting as BaseSetting;

/**
 * @ORM\Table(name="settings")
 * @ORM\Entity()
 */
class Setting extends BaseSetting
{
  const SETTING_ABANDONED_CART_AGE = 'abandoned_cart_age';
  /** Хранит id статуса товара. Этот статус ставится после оформления заказа. */
  const SETTING_DEFAULT_ORDER_STATUS = 'default_order_status';
  const SETTING_DEFAULT_ORDER_PAYMENT_STATUS = 'default_order_payment_status';
}