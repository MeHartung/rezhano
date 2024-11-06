<?php

namespace StoreBundle\Entity\Store\Logistics\Delivery\Cdek;

use Doctrine\ORM\Mapping as ORM;

/**
 * CdekRawPvzlist
 *
 * @ORM\Table(name="cdek_pvzlist")
 * @ORM\Entity
 */
class CdekRawPvzlist
{
  /**
   * @var string
   * @ORM\Id
   * @ORM\Column(length=255)
   * @ORM\GeneratedValue(strategy="NONE")
   */
  private $code;


  /**
   * @var string
   *
   * @ORM\Column(length=255)
   */
  private $owner_code;

  /**
   * @var string
   *
   * @ORM\Column(length=255)
   */
  private $coord_y;

  /**
   * @var string
   *
   * @ORM\Column(length=255)
   */
  private $coord_x;

  /**
   * @var string
   *
   * @ORM\Column(length=255)
   */
  private $work_time;

  /**
   * @var string
   *
   * @ORM\Column(length=255)
   */
  private $city_name;

  /**
   * @var string
   *
   * @ORM\Column(length=255)
   */
  private $city_code;


  /**
   * @var string
   *
   * @ORM\Column(length=255)
   */
  private $type;

  /**
   * @var string
   *
   * @ORM\Column(length=255)
   */
  private $note;

  /**
   * @var string
   *
   * @ORM\Column(length=255)
   */
  private $phone;

  /**
   * @var string
   *
   * @ORM\Column(length=255)
   */
  private $address;

  /**
   * @var string
   *
   * @ORM\Column(length=255)
   */
  private $name;


  public function fromArray($v)
  {
    if (isset($v['ownerCode']))
    {
      $this->setOwnerCode($v['ownerCode']);
    }
    if (isset($v['Type']))
    {
      $this->setType($v['Type']);
    }
    if (isset($v['coordY']))
    {
      $this->setCoordY($v['coordY']);
    }
    if (isset($v['coordX']))
    {
      $this->setCoordX($v['coordX']);
    }
    if (isset($v['Note']))
    {
      $this->setNote($v['Note']);
    }
    if (isset($v['Phone']))
    {
      $this->setPhone($v['Phone']);
    }
    if (isset($v['Address']))
    {
      $this->setAddress($v['Address']);
    }
    if (isset($v['WorkTime']))
    {
      $this->setWorkTime($v['WorkTime']);
    }
    if (isset($v['City']))
    {
      $this->setCityName($v['City']);
    }
    if (isset($v['CityCode']))
    {
      $this->setCityCode($v['CityCode']);
    }
    if (isset($v['Name']))
    {
      $this->setName($v['Name']);
    }
    if (isset($v['Code']))
    {
      $this->setCode($v['Code']);
    }
  }


  /**
   * Set code
   *
   * @param string $code
   *
   * @return CdekRawPvzlist
   */
  public function setCode($code)
  {
    $this->code = $code;

    return $this;
  }

  /**
   * Get code
   *
   * @return string
   */
  public function getCode()
  {
    return $this->code;
  }

  /**
   * Set ownerCode
   *
   * @param string $ownerCode
   *
   * @return CdekRawPvzlist
   */
  public function setOwnerCode($ownerCode)
  {
    $this->owner_code = $ownerCode;

    return $this;
  }

  /**
   * Get ownerCode
   *
   * @return string
   */
  public function getOwnerCode()
  {
    return $this->owner_code;
  }

  /**
   * Set type
   *
   * @param string $type
   *
   * @return CdekRawPvzlist
   */
  public function setType($type)
  {
    $this->type = $type;

    return $this;
  }

  /**
   * Get type
   *
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }

  /**
   * Set coordY
   *
   * @param string $coordY
   *
   * @return CdekRawPvzlist
   */
  public function setCoordY($coordY)
  {
    $this->coord_y = $coordY;

    return $this;
  }

  /**
   * Get coordY
   *
   * @return string
   */
  public function getCoordY()
  {
    return $this->coord_y;
  }

  /**
   * Set coordX
   *
   * @param string $coordX
   *
   * @return CdekRawPvzlist
   */
  public function setCoordX($coordX)
  {
    $this->coord_x = $coordX;

    return $this;
  }

  /**
   * Get coordX
   *
   * @return string
   */
  public function getCoordX()
  {
    return $this->coord_x;
  }

  /**
   * Set note
   *
   * @param string $note
   *
   * @return CdekRawPvzlist
   */
  public function setNote($note)
  {
    $this->note = $note;

    return $this;
  }

  /**
   * Get note
   *
   * @return string
   */
  public function getNote()
  {
    return $this->note;
  }

  /**
   * Set phone
   *
   * @param string $phone
   *
   * @return CdekRawPvzlist
   */
  public function setPhone($phone)
  {
    $this->phone = $phone;

    return $this;
  }

  /**
   * Get phone
   *
   * @return string
   */
  public function getPhone()
  {
    return $this->phone;
  }

  /**
   * Set address
   *
   * @param string $address
   *
   * @return CdekRawPvzlist
   */
  public function setAddress($address)
  {
    $this->address = $address;

    return $this;
  }

  /**
   * Get address
   *
   * @return string
   */
  public function getAddress()
  {
    return $this->address;
  }

  /**
   * Set workTime
   *
   * @param string $workTime
   *
   * @return CdekRawPvzlist
   */
  public function setWorkTime($workTime)
  {
    $this->work_time = $workTime;

    return $this;
  }

  /**
   * Get workTime
   *
   * @return string
   */
  public function getWorkTime()
  {
    return $this->work_time;
  }

  /**
   * Set cityName
   *
   * @param string $cityName
   *
   * @return CdekRawPvzlist
   */
  public function setCityName($cityName)
  {
    $this->city_name = $cityName;

    return $this;
  }

  /**
   * Get cityName
   *
   * @return string
   */
  public function getCityName()
  {
    return $this->city_name;
  }

  /**
   * Set cityCode
   *
   * @param string $cityCode
   *
   * @return CdekRawPvzlist
   */
  public function setCityCode($cityCode)
  {
    $this->city_code = $cityCode;

    return $this;
  }

  /**
   * Get cityCode
   *
   * @return string
   */
  public function getCityCode()
  {
    return $this->city_code;
  }

  /**
   * Set name
   *
   * @param string $name
   *
   * @return CdekRawPvzlist
   */
  public function setName($name)
  {
    $this->name = $name;

    return $this;
  }

  /**
   * Get name
   *
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
}
