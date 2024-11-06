<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Entity\Store\Catalog\Product;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Сущность "Вопрос по товару"
 *
 * @package StoreBundle\Entity\Store\Catalog\Product
 * @ORM\Entity()
 * @ORM\Table(name="product_questions")
 */
class ProductQuestion
{
  /**
   * @var int
   *
   * @ORM\Column(type="integer")
   * @ORM\Id()
   * @ORM\GeneratedValue()
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(length=255)
   * @Assert\NotBlank()
   */
  private $name;

  /**
   * @var string
   *
   * @ORM\Column(nullable=true)
   * @Assert\Email()
   */
  private $email;

  /**
   * @var string
   *
   * @ORM\Column(nullable=true)
   */
  private $phone;

  /**
   * @var string
   *
   * @ORM\Column(type="text")
   * @Assert\NotBlank()
   */
  private $text;

  /**
   * @var Product
   *
   * @ORM\ManyToOne(targetEntity="StoreBundle\Entity\Store\Catalog\Product\Product")
   * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
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
   * @return ProductQuestion
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
   * @return ProductQuestion
   */
  public function setName($name)
  {
    $this->name = $name;
    return $this;
  }

  /**
   * @return string
   */
  public function getEmail()
  {
    return $this->email;
  }

  /**
   * @param string $email
   * @return ProductQuestion
   */
  public function setEmail($email)
  {
    $this->email = $email;
    return $this;
  }

  /**
   * @return string
   */
  public function getPhone()
  {
    return $this->phone;
  }

  /**
   * @param string $phone
   * @return ProductQuestion
   */
  public function setPhone($phone)
  {
    $this->phone = $phone;
    return $this;
  }

  /**
   * @return string
   */
  public function getText()
  {
    return $this->text;
  }

  /**
   * @param string $text
   * @return ProductQuestion
   */
  public function setText($text)
  {
    $this->text = $text;
    return $this;
  }

  /**
   * @return Product
   */
  public function getProduct()
  {
    return $this->product;
  }

  /**
   * @param Product $product
   * @return ProductQuestion
   */
  public function setProduct($product)
  {
    $this->product = $product;
    return $this;
  }
}