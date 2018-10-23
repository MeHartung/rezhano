<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Entity\User;

use StoreBundle\Entity\Catalog\ProductList\FavoriteProductList;
use StoreBundle\Entity\Catalog\ProductList\ViewedProductList;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use FOS\UserBundle\Model\User as BaseUser;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use StoreBundle\Entity\Document\UserDocument;
use StoreBundle\Entity\Notification\Notification;
use StoreBundle\Entity\Store\Logistics\Delivery\Cdek\CdekCity;
use Symfony\Component\Validator\Constraints as Assert;
use StoreBundle\Validator\Constraints as StoreAssert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

/**
 * Пользователь
 *
 * @package StoreBundle\Entity\User
 * @ORM\Table(name="users")
 * @ORM\Entity()
 * @StoreAssert\JuridicalUserCompanyRequired()
 * @StoreAssert\UserRoles()
 * @DoctrineAssert\UniqueEntity(fields={"email"}, message="Пользователь с таким E-mail уже зарегистрирован", errorPath="#")
 */
class User extends BaseUser
{
  /*
   * Физическое лицо
   */
  const ROLE_INDIVIDUAL = 'ROLE_INDIVIDUAL';
  /*
   * Юридическое лицо
   */
  const ROLE_JURIDICAL = 'ROLE_JURIDICAL';
  /*
   * Индивидуальный предприниматель
   */
  const ROLE_ENTREPRENEUR = 'ROLE_ENTREPRENEUR';

  const ROLE_ADMIN = 'ROLE_ADMIN';
  /**
   * @var int
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue
   */
  protected $id;

  /**
   * @var string
   * @ORM\Column(type="string", length=50, nullable=true)
   */
  private $firstname;
  /**
   * @var string
   * @ORM\Column(type="string", length=50, nullable=true)
   */
  private $lastname;
  /**
   * @var string
   * @ORM\Column(type="string", length=50, nullable=true)
   */
  private $middlename;
  /**
   * @var string
   * @ORM\Column(type="string", length=50, nullable=true)
   */
  private $phone;

  /**
   * @var ArrayCollection
   * @ORM\OneToMany(targetEntity="StoreBundle\Entity\Store\Order\Order", mappedBy="user")
   */
  private $orders;

  /**
   * @var Company
   * @ORM\ManyToOne(targetEntity="StoreBundle\Entity\User\Company", inversedBy="users", cascade={"persist", "remove"})
   * @ORM\JoinColumn(nullable=true)
   */
  private $company;

  /**
   * @var
   * @Assert\Length(
   *      min = 5,
   *      max = 255,
   *      minMessage = "Пароль не может быть короче {{ limit }} символов",
   *      maxMessage = "Пароль не может быть длинее {{ limit }} символов"
   * )
   */
  protected $plainPassword;

  /**
   * @var ViewedProductList[]
   * @ORM\OneToMany(targetEntity="StoreBundle\Entity\Catalog\ProductList\ViewedProductList", mappedBy="user", cascade={"persist"})
   */
  private $viewedProductLists;

  /**
   * @var FavoriteProductList[]
   * @ORM\OneToMany(targetEntity="StoreBundle\Entity\Catalog\ProductList\FavoriteProductList", mappedBy="user", cascade={"persist"})
   */
  private $favoriteProductLists;

  /**
   * @var CdekCity|null
   * @ORM\ManyToOne(targetEntity="StoreBundle\Entity\Store\Logistics\Delivery\Cdek\CdekCity", inversedBy="users")
   * @ORM\JoinColumn(nullable=true, name="city_id", onDelete="SET NULL")
   */
  private $city;

  /**
   * @var boolean
   * @ORM\Column(type="boolean", name="is_contragent", nullable=false, options={"default"=false})
   */
  private $contragent=false;

  /**
   * @var UserDocument[]|ArrayCollection
   * @ORM\OneToMany(targetEntity="StoreBundle\Entity\Document\UserDocument", mappedBy="user", cascade={"persist"})
   */
  private $documents;

  /**
   * @var Notification[]|ArrayCollection
   * @ORM\OneToMany(targetEntity="StoreBundle\Entity\Notification\Notification", mappedBy="user")
   * @ORM\OrderBy(value={"read"="ASC","createdAt"="DESC"})
   */
  private $notifications;

  public function __construct ()
  {
    $this->viewedProductLists = new ArrayCollection();
    $this->documents = new ArrayCollection();
    $this->notifications = new ArrayCollection();
    parent::__construct();
  }

  /**
   * @return string
   */
  public function getFirstName ()
  {
    return $this->firstname;
  }

  /**
   * @param string $firstname
   * @return User
   */
  public function setFirstName ($firstname)
  {
    $this->firstname = $firstname;
    return $this;
  }

  /**
   * @return string
   */
  public function getLastName ()
  {
    return $this->lastname;
  }

  /**
   * @param string $lastname
   * @return User
   */
  public function setLastName ($lastname)
  {
    $this->lastname = $lastname;
    return $this;
  }

  /**
   * @return string
   */
  public function getMiddleName ()
  {
    return $this->middlename;
  }

  /**
   * @param string $middlename
   * @return User
   */
  public function setMiddleName ($middlename)
  {
    $this->middlename = $middlename;
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
   * @return User
   */
  public function setPhone ($phone)
  {
    $this->phone = $phone;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setEmail ($email)
  {
    $this->setUsername($email);
    return parent::setEmail($email);
  }

  /**
   * @return bool
   */
  public function isWholesale ()
  {
    return $this->hasRole(static::ROLE_JURIDICAL);
  }

  /**
   * @return ArrayCollection
   */
  public function getOrders ()
  {
    return $this->orders;
  }

  public function getFio ()
  {
    $str = '';

    $str = $str . ' ' . (string)$this->getFirstName();
    $str = $str . ' ' . (string)$this->getLastName();
    $str = $str . ' ' . (string)$this->getMiddleName();

    return trim($str);
  }

  /**
   * @param ArrayCollection $orders
   */
  public function setOrders (ArrayCollection $orders)
  {
    $this->orders = $orders;
  }

  public function getRoles ()
  {
    $roles = parent::getRoles();

    return array_unique($roles);
  }

  /**
   * Возвращает ФИО пользователя
   *
   * @return string
   */
  public function getFullName ()
  {
    $nameParts = [];
    if ($this->getFirstName())
      $nameParts[] = $this->getFirstName();
    if ($this->getLastName())
      $nameParts[] = $this->getLastName();
    if ($this->getMiddleName())
      $nameParts[] = $this->getMiddleName();

    return implode(' ', $nameParts);
  }

  /**
   * @return ViewedProductList
   */
  public function getViewedProductList ()
  {
    $lists = $this->viewedProductLists;

    if (!count($lists))
    {
      $list = new ViewedProductList();
      $list->setUser($this);
      $this->viewedProductLists[] = $list;
      return $list;
    }

    return $this->viewedProductLists->first();
  }

  /**
   * @return FavoriteProductList
   */
  public function getFavoriteProductList ()
  {
    $lists = $this->favoriteProductLists;

    if (!count($lists))
    {
      $list = new FavoriteProductList();
      $list->setUser($this);
      $this->favoriteProductLists[] = $list;
      return $list;
    }

    return $this->favoriteProductLists->first();
  }

  /**
   * @return array
   */
  public static function getAvailableRoles ()
  {
    return [
      static::ROLE_ADMIN => static::ROLE_ADMIN,
      static::ROLE_JURIDICAL => static::ROLE_JURIDICAL,
      static::ROLE_ENTREPRENEUR => static::ROLE_ENTREPRENEUR,
      static::ROLE_INDIVIDUAL => static::ROLE_INDIVIDUAL,
    ];
  }

  /**
   * @return Company
   */
  public function getCompany ()
  {
    return $this->company;
  }

  /**
   * @param Company $company
   * @return $this
   */
  public function setCompany (Company $company)
  {
    $this->company = $company;
    return $this;
  }

  /**
   * Возвращает имя юзера для отображения там, где это требуется.
   *
   * Если в профиле указано ФИО пользователя, возвращает его. В противном случае возвращает логин пользователя (email)
   *
   * @return string
   */
  public function getDisplayName()
  {
    $displayName = $this->getFio();
    if (!strlen(trim($displayName)))
    {
      $displayName = $this->getUsername();
    }

    return $displayName;
  }

  /**
   * @return CdekCity|null
   */
  public function getCity ()
  {
    return $this->city;
  }

  /**
   * @param CdekCity|null $city
   * @return $this
   */
  public function setCity (CdekCity $city = null)
  {
    $this->city = $city;
    return $this;
  }

  /**
   * @return bool
   */
  public function isContragent ()
  {
    return $this->contragent;
  }

  /**
   * @param bool $contragent
   * @return $this
   */
  public function setContragent ($contragent)
  {
    $this->contragent = $contragent;
    return $this;
  }

  /**
   * @return UserDocument[]|ArrayCollection
   */
  public function getDocuments ()
  {
    return $this->documents;
  }

  /**
   * @param UserDocument[]|ArrayCollection $documents
   * @return $this
   */
  public function setDocuments ($documents)
  {
    foreach ($documents as $document)
    {
      $this->addDocument($document);
    }

    return $this;
  }

  public function addDocument(UserDocument $document)
  {
    $this->documents->add($document);
    $document->setUser($this);

    return $this;
  }

  public function removeDocument(UserDocument $document)
  {
    $this->documents->removeElement($document);
    return $this;
  }

  /**
   * @param null|integer $limit
   * @return ArrayCollection|Notification[]
   */
  public function getNotifications ($limit=null)
  {
    $criteria = Criteria::create();
    $criteria->orderBy(['read'=>'ASC', 'createdAt'=>'DESC']);

    if ($limit)
    {
      $criteria->setMaxResults($limit);
    }

    return $this->notifications->matching($criteria);
  }

  public function getNewNotifications ()
  {
    $notifications = $this->notifications;

    $criteria = Criteria::create();
    $criteria->where(
      Criteria::expr()->eq('read', false)
    );

    return $notifications->matching($criteria);
  }

  public static function getAvailableContragentStatuses()
  {
    return [
      static::ROLE_JURIDICAL => static::ROLE_JURIDICAL,
      static::ROLE_ENTREPRENEUR => static::ROLE_ENTREPRENEUR,
      static::ROLE_INDIVIDUAL => static::ROLE_INDIVIDUAL,
    ];
  }

  /**
   * @return string|null
   */
  public function getContragentStatus()
  {
    $currentStatuses = array_intersect($this->getRoles(), self::getAvailableContragentStatuses());

    return count($currentStatuses)?reset($currentStatuses):null;
  }

  /**
   * @param string|null $status
   * @return $this
   */
  public function setContragentStatus($status)
  {
    $statuses = self::getAvailableContragentStatuses();

    if (!is_null($status) && !in_array($status, $statuses))
    {
      throw new \InvalidArgumentException(sprintf('Available statuses: [%s]', implode(' ,', $statuses)));
    }

    foreach ($statuses as $item)
    {
      $this->removeRole($item);
    }

    if (!is_null($status))
    {
      $this->addRole($status);
    }

    return $this;
  }
}