<?php

namespace Accurateweb\SettingBundle\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Setting
 * @package Accurateweb\SettingBundle\Model
 * @ORM\MappedSuperclass()
 */
abstract class Setting implements SettingEntityInterface
{
  /**
   * @var $name string
   * @ORM\Column(type="string", length=50, unique=true)
   * @ORM\Id()
   */
  protected $name;

  /**
   * @var string
   * @ORM\Column(type="text", nullable=true)
   */
  protected $value;

  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }

  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }


  public function setValue($value)
  {
    $this->value = $value;
    return $this;
  }
}