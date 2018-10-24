<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Entity\Store\Catalog\Taxonomy;

use Accurateweb\MediaBundle\Model\Image\ImageAwareInterface;
use Accurateweb\MediaBundle\Model\Media\ImageInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use StoreBundle\Entity\Store\Catalog\Product\Product;
use StoreBundle\Media\Store\Catalog\Taxonomy\TaxonImage;
use StoreBundle\Sluggable\SluggableInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use StoreBundle\Validator\Constraints as StoreAssert;

/**
 * Раздел каталога
 *
 * @Gedmo\Tree(type="nested")
 * @ORM\Table(name="catalog_sections")
 * @ORM\Entity(repositoryClass="StoreBundle\Repository\Store\Catalog\Taxonomy\TaxonRepository")
 * @ORM\HasLifecycleCallbacks()
 * @DoctrineAssert\UniqueEntity(fields={"slug"})
 * @StoreAssert\NotSelfLinkedTaxon
 */
class Taxon implements SluggableInterface, ImageAwareInterface
{

  /**
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(length=255)
   */
  private $slug;

  /**
   * @ORM\Column(name="name", type="string", length=255)
   */
  private $name;

  /**
   * @var string
   *
   * @ORM\Column(type="text", nullable=true)
   */
  private $description;

  /**
   * @Gedmo\TreeLeft
   * @ORM\Column(name="tree_left", type="integer")
   */
  private $treeLeft;

  /**
   * @Gedmo\TreeLevel
   * @ORM\Column(name="tree_level", type="integer")
   */
  private $treeLevel = 0;

  /**
   * @Gedmo\TreeRight
   * @ORM\Column(name="tree_right", type="integer")
   */
  private $treeRight;

  /**
   * @var string
   *
   * @ORM\Column(name="image", length=255, nullable=true)
   */
  private $teaser;

  /**
   * @Gedmo\TreeRoot
   * @ORM\ManyToOne(targetEntity="Taxon")
   * @ORM\JoinColumn(name="tree_root", referencedColumnName="id", onDelete="CASCADE")
   */
  private $root;

  /**
   * @Gedmo\TreeParent
   * @ORM\ManyToOne(targetEntity="Taxon", inversedBy="children", cascade={"persist", "remove"})
   * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $parent;

  /**
   * @ORM\OneToMany(targetEntity="Taxon", mappedBy="parent")
   * @ORM\OrderBy({"treeLeft" = "ASC"})
   */
  private $children;


  /**
   * @ORM\ManyToMany(targetEntity="StoreBundle\Entity\Store\Catalog\Product\Product", cascade={"persist", "remove"}, orphanRemoval=true, mappedBy="taxons")
   */
  private $products;

  private $sphinxWeight;

  /**
   * @var PresentationOptions
   * @ORM\OneToOne(targetEntity="StoreBundle\Entity\Store\Catalog\Taxonomy\PresentationOptions", mappedBy="taxon", cascade={"persist", "remove"})
   */
  private $presentationOptions;

  /**
   * @ORM\Column(name="short_name", type="string", length=255)
   * @var $shortName string
   */
  private $shortName;

  /**
   * @var integer
   * @ORM\Column(type="integer", nullable=true)
   */
  private $presentationId;

  /**
   * @var Taxon[]|ArrayCollection
   * @ORM\ManyToMany(targetEntity="StoreBundle\Entity\Store\Catalog\Taxonomy\Taxon", inversedBy="linkedByTaxons")
   * @ORM\JoinTable(name="catalog_section_linked",
   *      joinColumns={@ORM\JoinColumn(name="catalog_section_id", referencedColumnName="id")},
   *      inverseJoinColumns={@ORM\JoinColumn(name="catalog_section_linked_id", referencedColumnName="id")}
   *      )
   */
  private $linkedTaxons;

  /**
   * @var Taxon[]|ArrayCollection
   * @ORM\ManyToMany(targetEntity="StoreBundle\Entity\Store\Catalog\Taxonomy\Taxon", mappedBy="linkedTaxons")
   */
  private $linkedByTaxons;

  /**
   * @var integer
   * @ORM\Column(type="integer", nullable=false, options={"default"=0})
   */
  private $nbProducts=0;

  function __construct()
  {
    $this->products = new ArrayCollection();
    $this->linkedTaxons = new ArrayCollection();
    $this->linkedByTaxons = new ArrayCollection();
  }

  public function getId()
  {
    return $this->id;
  }

  public function setName($name)
  {
    $this->name = $name;

    return $this;
  }

  public function getName()
  {
    return $this->name;
  }

  public function getRoot()
  {
    return $this->root;
  }

  public function setParent(Taxon $parent = null)
  {
    $this->parent = $parent;

    return $this;
  }

  public function getParent()
  {
    return $this->parent;
  }

  /**
   * @return string
   */
  public function getSlug()
  {
    return $this->slug;
  }

  /**
   * @param string $slug
   * @return Taxon
   */
  public function setSlug($slug)
  {
    $this->slug = $slug;
    return $this;
  }

  /**
   * @return mixed
   */
  public function getDescription()
  {
    return $this->description;
  }

  /**
   * @param mixed $description
   * @return Taxon
   */
  public function setDescription($description)
  {
    $this->description = $description;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getTeaser()
  {
    return $this->teaser;
  }

  /**
   * @param mixed $teaser
   * @return Taxon
   */
  public function setTeaser($teaser)
  {
    /*
     * Не даем сбрасывать изображение из-за пустого значения в форме
     */
    if (null !== $teaser)
    {
      $this->teaser = $teaser;
    }

    return $this;
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
   * @return ArrayCollection
   */
  public function getProducts()
  {
    return $this->products;
  }

  /**
   * @param Product $product
   * @return $this
   */
  public function addProduct(Product $product)
  {
    if (!$product->getTaxons()->contains($this))
    {
      $product->addTaxon($this);
    }

    $this->products->add($product);
    return $this;
  }

  public function isRoot()
  {
    return $this->treeLeft == 1;
  }

  public function setSphinxWeight($v)
  {
    $this->sphinxWeight = $v;
  }

  public function getSphinxWeight()
  {
    return $this->sphinxWeight;
  }

  public function compare(Taxon $_b)
  {
    if ($this->getName() == $_b->getName())
    {
      return 0;
    }

    return ($this->getName() < $_b->getName()) ? -1 : 1;
  }

  /**
   * @return mixed
   */
  public function getTreeLeft()
  {
    return $this->treeLeft;
  }

  /**
   * @return mixed
   */
  public function getTreeLevel()
  {
    return $this->treeLevel;
  }

  /**
   * @return mixed
   */
  public function getTreeRight()
  {
    return $this->treeRight;
  }
  /*
   * получаем имя раздела каталога в виде строки.
   */
  public function __toString()
  {
    return (string)$this->getStringName();
  }

  public function getStringName()
  {
    $prefix = "";
    for ($i=2; $i <= $this->treeLevel; $i++){
      $prefix .= " ";
    }

    return $prefix . $this->name;
  }

  /**
   * @ORM\PreUpdate()
   * @ORM\PreRemove()
   *
   * @return $this
   */
/*  public function checkRootModifiation()
  {
    if ($this->isRoot())
    {
      throw new InvalidOperationException('Невозможно удалить или изменть корневой раздел');
    }

    return $this;
  }*/

  public function getSlugSource()
  {
    return $this->getName();
  }

  /**
   * @param $id
   * @return ImageInterface
   */
  public function getImage($id = null)
  {
    if (!$this->teaser)
    {
      return null;
    }

    return new TaxonImage('teaser', $this->teaser);
  }

  /**
   * @param ImageInterface $image
   * @return Taxon
   */
  public function setImage(ImageInterface $image)
  {
    $this->teaser = $image ? $image->getResourceId() : null;
    return $this;
  }


  /**
   * @param $id
   * @return mixed
   */
  public function getImageOptions($id)
  {
    // TODO: Implement getImageOptions() method.
  }

  public function setImageOptions($id)
  {
    // TODO: Implement setImageOptions() method.
  }

  /**
   * для Jstree
   */

  public function getClientModelId()
  {
    return $this->getId();
  }

  public function getClientModelName()
  {
    return 'CatalogSection';
  }

  /**
   * @return string
   */
  public function getShortName()
  {
    return $this->shortName;
  }

  /**
   * @param string $shortName
   * @return Taxon
   */
  public function setShortName($shortName)
  {
    is_null($shortName) ? $this->shortName = $this->getName() : $this->shortName = $shortName;
    return $this;
  }

  /**
   * @ORM\PrePersist()
   */
  public function setDeafaultShortName()
  {
    if(!$this->getShortName())
    {
      $this->setShortName($this->getName());
    }
  }

    /**
     * @param mixed $treeLeft
     */
    public function setTreeLeft($treeLeft)
    {
        $this->treeLeft = $treeLeft;
    }

    /**
     * @param mixed $treeLevel
     */
    public function setTreeLevel($treeLevel)
    {
        $this->treeLevel = $treeLevel;
    }

    /**
     * @param mixed $treeRight
     */
    public function setTreeRight($treeRight)
    {
        $this->treeRight = $treeRight;
    }

    /**
     * @return int
     */
    public function getPresentationId ()
    {
      return $this->presentationId;
    }

    /**
     * @param int $presentationId
     * @return $this
     */
    public function setPresentationId (int $presentationId)
    {
      $this->presentationId = $presentationId;
      return $this;
    }

    /**
     * @return PresentationOptions
     */
    public function getPresentationOptions ()
    {
      if (!$this->presentationOptions)
      {
        $this->presentationOptions = new PresentationOptions();
        $this->presentationOptions->setTaxon($this);
      }

      return $this->presentationOptions;
    }

    /**
     * @param PresentationOptions $presentationOptions
     * @return $this
     */
    public function setPresentationOptions (PresentationOptions $presentationOptions)
    {
      $this->presentationOptions = $presentationOptions;
      $presentationOptions->setTaxon($this);
      return $this;
    }

    /**
     * @return ArrayCollection|Taxon[]
     */
    public function getLinkedTaxons ()
    {
      $criteria = Criteria::create();
      $criteria
        ->where(Criteria::expr()->gt(
          'nbProducts', 0
        ));

      return $this->linkedTaxons->matching($criteria);
    }

    /**
     * @param ArrayCollection|Taxon[] $linkedTaxons
     * @return $this
     */
    public function setLinkedTaxons ($linkedTaxons)
    {
      $this->linkedTaxons = $linkedTaxons;
      return $this;
    }

    /**
     * @return ArrayCollection|Taxon[]
     */
    public function getLinkedByTaxons ()
    {
      return $this->linkedByTaxons;
    }

    /**
     * @return int
     */
    public function getNbProducts ()
    {
      return $this->nbProducts;
    }

    /**
     * @param int $nbProducts
     * @return $this
     */
    public function setNbProducts ($nbProducts)
    {
      $this->nbProducts = $nbProducts;
      return $this;
    }

}