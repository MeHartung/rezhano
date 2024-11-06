<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace AccurateCommerce\Component\Payment\Model;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Description of PaymentMethod
 *
 * @package AccurateCommerce\Component\Payment\Method
 *
 * @ORM\MappedSuperclass()
 */
abstract class PaymentMethod implements PaymentMethodInterface
{
  /**
   * @var string
   *
   * @ORM\Column(length=36)
   */
  protected $availabilityDecisionManagerId;

  /**
   * @var string
   *
   * @ORM\Column(length=36)
   */
  protected $feeCalculatorId;

  /**
   * @var string
   *
   * @ORM\Column(length=255)
   */
  protected $name;

  /**
   * @var string
   *
   * @ORM\Column(length=512, nullable=true)
   */
  protected $description;

  /**
   * @var boolean
   *
   * @ORM\Column(type="boolean")
   */
  protected $enabled;

  /**
   * @var int
   *
   * @Gedmo\SortablePosition
   * @ORM\Column(type="integer")
   */
  protected $position;

  function __construct()
  {
    $this->enabled = true;
  }

  /**
   * @return string
   */
  public function getAvailabilityDecisionManagerId()
  {
    return $this->availabilityDecisionManagerId;
  }

  /**
   * @param string $availabilityDecisionManagerId
   * @return PaymentMethod
   */
  public function setAvailabilityDecisionManagerId($availabilityDecisionManagerId)
  {
    $this->availabilityDecisionManagerId = $availabilityDecisionManagerId;

    return $this;
  }

  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * @param string $name
   * @return PaymentMethod
   */
  public function setName($name)
  {
    $this->name = $name;

    return $this;
  }

  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }

  /**
   * @param string $description
   * @return PaymentMethod
   */
  public function setDescription($description)
  {
    $this->description = $description;

    return $this;
  }

  /**
   * @return bool
   */
  public function isEnabled()
  {
    return $this->enabled;
  }

  /**
   * @param bool $enabled
   * @return PaymentMethod
   */
  public function setEnabled($enabled)
  {
    $this->enabled = $enabled;

    return $this;
  }

  /**
   * @return int
   */
  public function getPosition()
  {
    return $this->position;
  }

  /**
   * @param int $position
   * @return PaymentMethod
   */
  public function setPosition($position)
  {
    $this->position = $position;

    return $this;
  }

  /**
   * @return string
   */
  public function getFeeCalculatorId()
  {
    return $this->feeCalculatorId;
  }

  /**
   * @param string $feeCalculatorId
   *
   * @return PaymentMethod
   */
  public function setFeeCalculatorId($feeCalculatorId)
  {
    $this->feeCalculatorId = $feeCalculatorId;

    return $this;
  }
}