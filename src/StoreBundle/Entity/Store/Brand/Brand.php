<?php

namespace StoreBundle\Entity\Store\Brand;

use Doctrine\ORM\Mapping as ORM;
use StoreBundle\Sluggable\SluggableInterface;

/**
 * Class Brand
 *
 * @package StoreBundle\Entity\Store\Brand
 *
 * @ORM\Entity()
 * @ORM\Table(name="brands")
 */
class Brand implements SluggableInterface
{
  /**
   * @var int
   * @ORM\Column(type="integer")
   * @ORM\Id()
   * @ORM\GeneratedValue()
   */
  private $id;

  /**
   * @var int
   *
   * @ORM\Column(type="integer", nullable=true)
   */
  private $virtuemartManufacturerId;

  /**
   * @var string
   *
   * @ORM\Column(length=255)
   */
  private $slug;

  /**
   * @var string
   *
   * @ORM\Column(length=255)
   */
  private $name;

  public function __construct()
  {
    $this->name = 'Новый бренд';
  }

  /**
   * @return int
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @param int $id
   * @return Brand
   */
  public function setId($id)
  {
    $this->id = $id;
    return $this;
  }

  /**
   * @return int
   */
  public function getVirtuemartManufacturerId()
  {
    return $this->virtuemartManufacturerId;
  }

  /**
   * @param int $virtuemartManufacturerId
   * @return Brand
   */
  public function setVirtuemartManufacturerId($virtuemartManufacturerId)
  {
    $this->virtuemartManufacturerId = $virtuemartManufacturerId;

    return $this;
  }

  public function getSlugSource()
  {
    return $this->getName();
  }

  public function getSlug()
  {
    return $this->slug;
  }

  public function setSlug($slug)
  {
    $this->slug = $slug;

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
   * @return Brand
   */
  public function setName($name)
  {
    $this->name = $name;

    return $this;
  }

  /**
   * @return string
   */
  public function __toString()
  {
    return $this->getName();
  }
}