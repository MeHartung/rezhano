<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 15.09.17
 * Time: 13:08
 */

namespace StoreBundle\Entity\Store\Catalog\Product;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class ProductToTaxon
 * @package StoreBundle\Entity\Store\Catalog\Product
 * @ORM\Table(name="products_to_taxons")
 */
class ProductToTaxon
{
  /**
   * @var int
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue
   **/
  private $id;

  /**
   * @var int
   *
   * @ORM\Column(type="integer")
   */
  private $taxon;

  /**
   * @var int
   *
   * @ORM\Column(type="integer")
   */
  private $product;

  /**
   * @return int
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @param int $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }

  /**
   * @return int
   */
  public function getTaxon()
  {
    return $this->taxon;
  }

  /**
   * @param int $taxon
   */
  public function setTaxon($taxon)
  {
    $this->taxon = $taxon;
  }

  /**
   * @return int
   */
  public function getProduct()
  {
    return $this->product;
  }

  /**
   * @param int $product
   */
  public function setProduct($product)
  {
    $this->product = $product;
  }

}