<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Entity\Text;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class ContactPhone
 * @package StoreBundle\Entity\Text
 *
 * @ORM\Entity()
 * @ORM\Table()
 */
class ContactPhone
{
  /**
   * @var integer
   *
   * @ORM\Column(type="integer")
   * @ORM\Id()
   * @ORM\GeneratedValue()
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column()
   */
  private $name;

  /**
   * @var string
   *
   * @ORM\Column()
   */
  private $phone;

  /**
   * @var boolean
   *
   * @ORM\Column(type="boolean", options={"default":0})
   */
  private $published = false;

  /**
   * @return int
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @param int $id
   * @return ContactPhone
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
   * @return ContactPhone
   */
  public function setName($name)
  {
    $this->name = $name;
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
   * @return ContactPhone
   */
  public function setPhone($phone)
  {
    $this->phone = $phone;
    return $this;
  }

  /**
   * @return bool
   */
  public function isPublished()
  {
    return $this->published;
  }

  /**
   * @param bool $published
   * @return ContactPhone
   */
  public function setPublished($published)
  {
    $this->published = $published;
    return $this;
  }

  public function __toString()
  {
    return $this->getName() ?: 'Новый контактный номер телефона';
  }

}