<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Entity\Store\Payment\Method;

use Doctrine\ORM\Mapping as ORM;

use AccurateCommerce\Component\Payment\Model\PaymentMethod as BasePaymentMethod;

/**
 * Description of PaymentMethod
 *
 * @package StoreBundle\Entity\Store\Payment\Method
 * @ORM\Table(name="payment_methods")
 * @ORM\Entity(repositoryClass="StoreBundle\Repository\Store\Payment\Method\PaymentMethodRepository")
 */
class PaymentMethod extends BasePaymentMethod
{
  /**
   * @var int
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue
   */
  private $id;

  /**
   * @var $type string
   * @ORM\Column(name="type_guid", type="string", length=255, nullable=true)
   */
  private $type;

  /**
   * @return int
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @param int $id
   * @return PaymentMethod
   */
  public function setId($id)
  {
    $this->id = $id;

    return $this;
  }

  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }

  /**
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }

  function __toString()
  {
    return $this->getName() ?: 'Новый способ оплаты';
  }
}