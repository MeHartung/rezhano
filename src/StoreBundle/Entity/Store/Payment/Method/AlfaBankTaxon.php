<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 24.11.17
 * Time: 13:48
 */

namespace StoreBundle\Entity\Store\Payment\Method;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Категории товаров для системы кредитования АльфаБанка
 * Class AlfaBankTaxon
 * @package StoreBundle\Entity\Store\Payment\Method
 * @ORM\Table(name="alfabank_credit_taxons")
 * @ORM\Entity(repositoryClass="StoreBundle\Repository\Store\Payment\Method\AlfaBankTaxonRepository")
 */
class AlfaBankTaxon
{

  /**
   * @var $id
   * @ORM\Id
   * @ORM\GeneratedValue
   * @ORM\Column(name="id", type="integer")
   */
  private $id;

  /**
   * @var $name string
   * @ORM\Column(name="name", type="string", length=255)
   */
  private $name;

  /**
   * @var $taxon
   * @ORM\OneToMany(targetEntity="StoreBundle\Entity\Store\Catalog\Taxonomy\Taxon", mappedBy="alfaBankTaxon")
   */
  private $taxons;

  /**
   * @var $description string
   * @ORM\Column(name="description", type="string", length=255)
   */
  private $description;

  /**
   * @return mixed
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @param mixed $id
   */
  public function setId($id)
  {
    $this->id = $id;
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
   */
  public function setName($name)
  {
    $this->name = $name;
  }

  /**
   * @return ArrayCollection
   */
  public function getTaxons()
  {
    return $this->taxons;
  }

  /**
   * @param ArrayCollection $taxons
   */
  public function setTaxons($taxons)
  {
    $this->taxons = $taxons;
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
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }

  public function __toString()
  {
    return (string)$this->getDescription();
  }
}