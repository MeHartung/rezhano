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

namespace AccurateCommerce\Component\Checkout\Event;

use StoreBundle\Entity\Store\Order\Order;
use Symfony\Component\EventDispatcher\Event;

/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 26.10.2017
 * Time: 21:08
 */
class OrderCheckoutEvent extends Event
{
  protected $order;

  public function __construct(Order $order)
  {
    $this->order = $order;
  }

  public function getOrder()
  {
    return $this->order;
  }
}