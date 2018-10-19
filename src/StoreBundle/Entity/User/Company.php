<?php

namespace StoreBundle\Entity\User;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="companies")
 * @ORM\Entity()
 */
class Company
{
  /**
   * @var int
   * @ORM\Id()
   * @ORM\GeneratedValue()
   * @ORM\Column(type="integer")
   */
  private $id;

  /**
   * @var string
   * @ORM\Column()
   */
  private $name;

  /**
   * @var string
   * @ORM\Column(type="string", length=12)
   */
  private $inn;

  /**
   * @var string
   * @ORM\Column(type="string", length=50)
   */
  private $kpp;

  /**
   * Основной государственный регистрационный номер
   * @var string
   * @ORM\Column(type="string", length=15)
   */
  private $ogrn;

  /**
   * @var string
   * @ORM\Column(type="string", length=50)
   */
  private $country;
  
  /**
   * @var string
   * @ORM\Column(type="string", length=255)
   */
  private $address;
  
  /**
   * @var string
   * @ORM\Column(type="string", length=255)
   */
  private $director;

  /**
   * @var string
   * @ORM\Column(type="string", length=50)
   */
  private $phone;

  /**
   * @var string
   * @ORM\Column(type="string", length=50)
   */
  private $email;

  /**
   * @var User[]|ArrayCollection
   * @ORM\OneToMany(targetEntity="StoreBundle\Entity\User\User", mappedBy="company")
   */
  private $users;

  /**
   * @return int
   */
  public function getId ()
  {
    return $this->id;
  }

  /**
   * @return string
   */
  public function getName ()
  {
    return $this->name;
  }

  /**
   * @param string $name
   * @return $this
   */
  public function setName (string $name)
  {
    $this->name = $name;
    return $this;
  }

  /**
   * @return string
   */
  public function getInn ()
  {
    return $this->inn;
  }

  /**
   * @param string $inn
   * @return $this
   */
  public function setInn (string $inn)
  {
    $this->inn = $inn;
    return $this;
  }

  /**
   * @return string
   */
  public function getKpp ()
  {
    return $this->kpp;
  }

  /**
   * @param string $kpp
   * @return $this
   */
  public function setKpp (string $kpp)
  {
    $this->kpp = $kpp;
    return $this;
  }

  /**
   * @return string
   */
  public function getOgrn ()
  {
    return $this->ogrn;
  }

  /**
   * @param string $ogrn
   * @return $this
   */
  public function setOgrn (string $ogrn)
  {
    $this->ogrn = $ogrn;
    return $this;
  }

  /**
   * @return string
   */
  public function getCountry ()
  {
    return $this->country;
  }

  /**
   * @param string $country
   * @return $this
   */
  public function setCountry (string $country)
  {
    $this->country = $country;
    return $this;
  }

  /**
   * @return string
   */
  public function getAddress ()
  {
    return $this->address;
  }

  /**
   * @param string $address
   * @return $this
   */
  public function setAddress (string $address)
  {
    $this->address = $address;
    return $this;
  }

  /**
   * @return string
   */
  public function getDirector ()
  {
    return $this->director;
  }

  /**
   * @param string $director
   * @return $this
   */
  public function setDirector (string $director)
  {
    $this->director = $director;
    return $this;
  }

  /**
   * @return string
   */
  public function getPhone ()
  {
    return $this->phone;
  }

  /**
   * @param string $phone
   * @return $this
   */
  public function setPhone (string $phone)
  {
    $this->phone = $phone;
    return $this;
  }

  /**
   * @return string
   */
  public function getEmail ()
  {
    return $this->email;
  }

  /**
   * @param string $email
   * @return $this
   */
  public function setEmail (string $email)
  {
    $this->email = $email;
    return $this;
  }


}