<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Entity\Text;

use Doctrine\ORM\Mapping as ORM;
use StoreBundle\Validator\Constraints as Assert;

/**
 * Class ContactPhone
 * @package StoreBundle\Entity\Text
 *
 * @ORM\Entity(repositoryClass="StoreBundle\Repository\Store\Text\ContactPhoneRepository")
 * @ORM\Table()
 * @Assert\ContactPlace
 */
class ContactPhone
{
  const SHOW_PLACE_HIDE = 0;
  const SHOW_PLACE_LEFT = 1;
  const SHOW_PLACE_RIGHT = 2;
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
   * @var integer
   * @ORM\Column(type="integer", nullable=false, options={"default"=0})
   */
  protected $showPlace;

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

  /**
   * @return int
   */
  public function getShowPlace ()
  {
    return $this->showPlace;
  }

  /**
   * @param int $showPlace
   * @return $this
   */
  public function setShowPlace ($showPlace)
  {
    if (!in_array($showPlace, self::getAvailableShowPlace()))
    {
      throw new \InvalidArgumentException(sprintf('Available places: [%s]', implode(', ', self::getAvailableShowPlace())));
    }

    $this->showPlace = $showPlace;
    return $this;
  }

  /**
   * Возвращает телефон, очищенный от символов, кроме цифр и +
   *
   * @return null|string|string[]
   */
  public function getCleanPhone()
  {
    return preg_replace('/[^\+\d]+/', '', $this->getPhone());
  }

  public function __toString()
  {
    return $this->getName() ?: 'Новый контактный номер телефона';
  }

  /**
   * @return array
   */
  public static function getAvailableShowPlace()
  {
    return [
      self::SHOW_PLACE_HIDE,
      self::SHOW_PLACE_LEFT,
      self::SHOW_PLACE_RIGHT
    ];
  }

}