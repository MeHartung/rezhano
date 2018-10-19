<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 02.08.17
 * Time: 15:06
 */

namespace StoreBundle\Entity\Menu;

use StoreBundle\Sluggable\SluggableInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;


/**
 * Меню хедера и футера
 * @Gedmo\Tree(type="nested")
 * @ORM\Table(name="menu")
 * @ORM\Entity(repositoryClass="StoreBundle\Repository\Menu\MenuItemRepository")
 * @ORM\HasLifecycleCallbacks()
 *
 */
class MenuItem
{
  /**
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue
   */
  private $id;

  /**
   * @ORM\Column(name="name", type="string", length=255)
   */
  private $name;

  /**
   * @var string
   *
   * @ORM\Column(length=255, nullable=true)
   */
  private $url;

  /**
   * @ORM\Column(name="head", type="boolean")
   */
  private $isHeaderDisplay;

  /**
   * @ORM\Column(name="foot", type="boolean")
   */
  private $isFooterDisplay;

  /**
   * @Gedmo\TreeLeft
   * @ORM\Column(name="tree_left", type="integer")
   */
  private $treeLeft;

  /**
   * @Gedmo\TreeLevel
   * @ORM\Column(name="tree_level", type="integer")
   */
  private $treeLevel;

  /**
   * @Gedmo\TreeRight
   * @ORM\Column(name="tree_right", type="integer")
   */
  private $treeRight;

  /**
   * @Gedmo\TreeRoot
   * @ORM\ManyToOne(targetEntity="MenuItem")
   * @ORM\JoinColumn(name="tree_root", referencedColumnName="id", onDelete="CASCADE")
   */
  private $root;

  /**
   * @Gedmo\TreeParent
   * @ORM\ManyToOne(targetEntity="MenuItem", inversedBy="children")
   * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $parent;

  /**
   * @ORM\OneToMany(targetEntity="MenuItem", mappedBy="parent")
   * @ORM\OrderBy({"treeLeft" = "ASC"})
   */
  private $children;

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
   * @return mixed
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * @param mixed $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }

  /**
   * @return mixed
   */
  public function getisHeaderDisplay()
  {
    return $this->isHeaderDisplay;
  }
  /**
   * @ORM\PrePersist()
   */
  public function setIsHeaderDisplayCreate()
  {
    if (!$this->isHeaderDisplay)
    {
      $this->isHeaderDisplay = 0;
    }
  }

  /**
   * @param mixed $isHeaderDisplay
   */
  public function setIsHeaderDisplay($isHeaderDisplay)
  {
    $this->isHeaderDisplay = $isHeaderDisplay;
  }

  /**
   * @return mixed
   */
  public function getIsFooterDisplay()
  {
    return $this->isFooterDisplay;
  }

  /**
   * @ORM\PrePersist()
   */
  public function setIsFooterDisplayCreate()
  {
    if (!$this->isFooterDisplay)
    {
      $this->isFooterDisplay = 0;
    }
  }

  /**
   * @param mixed $isFooterDisplay
   */
  public function setIsFooterDisplay($isFooterDisplay)
  {
    $this->isFooterDisplay = $isFooterDisplay;
  }

  /**
   * @return mixed
   */
  public function getTreeLeft()
  {
    return $this->treeLeft;
  }

  /**
   * @param mixed $treeLeft
   */
  public function setTreeLeft($treeLeft)
  {
    $this->treeLeft = $treeLeft;
  }

  /**
   * @return mixed
   */
  public function getTreeLevel()
  {
    return $this->treeLevel;
  }

  /**
   * @param mixed $treeLevel
   */
  public function setTreeLevel($treeLevel)
  {
    $this->treeLevel = $treeLevel;
  }

  /**
   * @return mixed
   */
  public function getTreeRight()
  {
    return $this->treeRight;
  }

  /**
   * @param mixed $treeRight
   */
  public function setTreeRight($treeRight)
  {
    $this->treeRight = $treeRight;
  }

  /**
   * @return mixed
   */
  public function getRoot()
  {
    return $this->root;
  }

  /**
   * @param mixed $root
   */
  public function setRoot($root)
  {
    $this->root = $root;
  }

  /**
   * @return mixed
   */
  public function getParent()
  {
    return $this->parent;
  }

  /**
   * @param mixed $parent
   */
  public function setParent($parent)
  {
    $this->parent = $parent;
  }

  /**
   * @return mixed
   */
  public function getChildren()
  {
    return $this->children;
  }

  /**
   * @param mixed $children
   */
  public function setChildren($children)
  {
    $this->children = $children;
  }

  /**
   * Для вывода тайтла родителя
   */
  public function __toString()
  {
    return $this->getPaddedName();
  }

  public function getPaddedName()
  {
    $prefix = "";
    for ($i=2; $i<= $this->treeLevel; $i++){
      $prefix .= " ";
    }
    return (string)$prefix . $this->name;
  }

  /**
   * Убираем пользователю возможность удалять/изменять/создавать корневой каталог
   *
   * @ORM\PreUpdate()
   * @ORM\PreRemove()
   * @param $treeLevel
   * @return $this
   */
  public function removeRoot($treeLevel)
  {
    if ($this->treeLevel == 0){

      throw new AccessDeniedHttpException('Невозможно удалить или изменть корневой каталог');

    }
    return $this;
  }

  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }

  /**
   * @param string $url
   * @return MenuItem
   */
  public function setUrl($url)
  {
    $this->url = $url;

    return $this;
  }


}