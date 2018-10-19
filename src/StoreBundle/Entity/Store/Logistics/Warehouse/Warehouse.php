<?php

namespace StoreBundle\Entity\Store\Logistics\Warehouse;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Accurateweb\LogisticBundle\Model\Warehouse as BaseWarehouse;
use StoreBundle\Entity\Store\Logistics\Delivery\Cdek\CdekCity;

/**
 * @ORM\Table(name="warehouses")
 * @ORM\Entity(repositoryClass="StoreBundle\Repository\Store\Logistics\Delivery\Warehouse\WarehouseRepository")
 */
class Warehouse extends BaseWarehouse
{
  /**
   * @var CdekCity
   * @ORM\ManyToOne(targetEntity="StoreBundle\Entity\Store\Logistics\Delivery\Cdek\CdekCity", inversedBy="warehouses", cascade={"persist"})
   * @ORM\JoinColumn(name="city_id", onDelete="CASCADE")
   */
  protected $city;

  /**
   * @var ProductStock[]|ArrayCollection
   * @ORM\OneToMany(targetEntity="StoreBundle\Entity\Store\Logistics\Warehouse\ProductStock", mappedBy="warehouse")
   */
  protected $productStocks;

  /**
   * @return CdekCity
   */
  public function getCity ()
  {
    return $this->city;
  }

  /**
   * @param CdekCity $city
   * @return $this
   */
  public function setCity (CdekCity $city)
  {
    $this->city = $city;
    return $this;
  }

  /**
   * @return ArrayCollection|ProductStock[]
   */
  public function getProductStocks ()
  {
    return $this->productStocks;
  }

  /**
   * @param ArrayCollection|ProductStock[] $productStocks
   * @return $this
   */
  public function setProductStocks ($productStocks)
  {
    $this->productStocks = $productStocks;
    return $this;
  }

  public function __toString ()
  {
    if (!$this->getName())
    {
      return 'Новый склад';
    }

    return $this->getName();
  }
}