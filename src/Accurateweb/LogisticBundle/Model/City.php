<?php

namespace Accurateweb\LogisticBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\MappedSuperclass()
 */
abstract class City implements CityInterface
{
  /**
   * @var integer
   * @ORM\Id()
   * @ORM\GeneratedValue()
   * @ORM\Column(type="integer")
   */
  protected $id;

  /**
   * @var string
   * @ORM\Column(type="string", length=255)
   */
  protected $name;

  /**
   * @var WarehouseInterface[]|ArrayCollection
   */
  protected $warehouses;
}