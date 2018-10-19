<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 25.06.2017
 * Time: 14:03
 */

namespace AccurateCommerce\Component\Payment\Method\Fee;


abstract class BaseFeeCalculator implements FeeCalculatorInterface
{
  private $id,
    $name,
    $description;

  function __construct($id, $name, $description='')
  {
    $this->setId($id);
    $this->setName($name);
    $this->setDescription($description);
  }

  /**
   * @return mixed
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @param mixed $id
   *
   * @return BaseFeeCalculator
   */
  public function setId($id)
  {
    $this->id = $id;

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
   * @return BaseFeeCalculator
   */
  public function setName($name)
  {
    $this->name = $name;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getDescription()
  {
    return $this->description;
  }

  /**
   * @param mixed $description
   * @return BaseFeeCalculator
   */
  public function setDescription($description)
  {
    $this->description = $description;

    return $this;
  }
}
