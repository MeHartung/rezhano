<?php

namespace StoreBundle\Entity\Store\Catalog\Taxonomy;

use AccurateCommerce\Exception\OperationNotSupportedException;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="presentation_options")
 * @ORM\Entity()
 */
class PresentationOptions implements \ArrayAccess
{
  /**
   * @var Taxon
   * @ORM\Id()
   * @ORM\OneToOne(targetEntity="StoreBundle\Entity\Store\Catalog\Taxonomy\Taxon", cascade={"persist"}, inversedBy="presentationOptions")
   * @ORM\JoinColumn(name="taxon_id")
   */
  private $taxon;

  /**
   * @var array
   * @ORM\Column(type="json_array", nullable=true)
   */
  private $options;

  public function __construct ()
  {
    $this->options = [];
  }

  /**
   * @return Taxon
   */
  public function getTaxon ()
  {
    return $this->taxon;
  }

  /**
   * @param Taxon $taxon
   * @return $this
   */
  public function setTaxon (Taxon $taxon)
  {
    $this->taxon = $taxon;
    return $this;
  }

  /**
   * @return array
   */
  public function getOptions ()
  {
    return $this->options;
  }

  /**
   * @param array $options
   * @return $this
   */
  public function setOptions (array $options)
  {
    $this->options = $options;
    return $this;
  }

  public function offsetExists($offset)
  {
    return array_key_exists($offset, $this->options);
  }

  public function offsetGet($offset)
  {
    return $this->options[$offset];
  }

  /**
   * @param mixed $offset
   * @param mixed $value
   * @throws OperationNotSupportedException
   */
  public function offsetSet($offset, $value)
  {
    $this->options[$offset] = $value;
  }

  /**
   * @param mixed $offset
   * @throws OperationNotSupportedException
   */
  public function offsetUnset($offset)
  {
    throw new OperationNotSupportedException('Unsetting option values is not supported. Use setOptions method instead.');
  }


}