<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Entity\Store\Catalog\Product;

use Accurateweb\LogisticBundle\Model\ProductStockInterface;
use Accurateweb\LogisticBundle\Model\StockableInterface;
use Accurateweb\MediaBundle\Annotation\Image;
use Accurateweb\MediaBundle\Model\Image\ImageAwareInterface;
use Accurateweb\MediaBundle\Model\Media\ImageInterface;
use Accurateweb\MediaBundle\Model\Media\MediaInterface;
use Accurateweb\MediaBundle\Model\Thumbnail\ImageThumbnail;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use StoreBundle\Entity\Store\Brand\Brand;
use StoreBundle\Entity\Store\Catalog\Product\Attributes\ProductAttribute;
use StoreBundle\Entity\Store\Catalog\Product\Attributes\ProductAttributeValue;
use StoreBundle\Entity\Store\Catalog\Product\Attributes\ProductAttributeValueToProduct;
use StoreBundle\Entity\Store\Catalog\Product\Attributes\Type\ProductType;
use StoreBundle\Entity\Store\Catalog\Taxonomy\Taxon;

use StoreBundle\Entity\Store\Logistics\Warehouse\ProductStock;
use StoreBundle\Entity\User\User;
use StoreBundle\Media\Text\UnprocessedImage;
use StoreBundle\Sluggable\SluggableInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Accurateweb\LogisticBundle\Validator\Constraints as LogisticAssert;

/**
 * Товар.
 *
 * @package StoreBundle\Entity\Store\Catalog\Product
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="products")
 * @ORM\Entity(repositoryClass="StoreBundle\Repository\Store\Catalog\Product\ProductRepository")
 *
 */
class Product implements SluggableInterface, ImageAwareInterface//, StockableInterface
{
  const ORANGE_BACKGROUND = 1;
  const BLACK_BACKGROUND = 2;
  /**
   * @var int
   *
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
   * @var string
   *
   * @ORM\Column(type="string", length=255)
   * @Assert\NotNull(message = "Поле не может быть пустым.");
   */
  private $name;

  /**
   * @var Brand
   *
   * @ORM\ManyToOne(targetEntity="StoreBundle\Entity\Store\Brand\Brand")
   */
  private $brand;

  /**
   * @var string
   *
   * @ORM\Column(name="short_description", length=1024)
   * @Assert\NotNull(message = "Поле не может быть пустым.");
   * @Assert\Length(
   *   min = 1,
   *   max = 1024,
   *   minMessage = "Описание не может быть короче {{ limit }} символа",
   *   maxMessage = "Описание не может быть длинее {{ limit }} символов"
   * )
   */
  private $shortDescription;

  /**
   * @var string
   *
   * @ORM\Column(type="text", nullable=true)
   */
  private $description;

  /**
   * @var float
   *
   * @ORM\Column(type="decimal", scale=2)
   */
  private $price;

  /**
   * @var float
   *
   * @ORM\Column(name="old_price", type="decimal", scale=2, nullable=true)
   */
  private $oldPrice;


  /**
   * @var bool
   *
   * @ORM\Column(name="is_sale", type="boolean", nullable=true)
   */
  private $sale;

  /**
   * @var bool
   *
   * @ORM\Column(name="is_hit", type="boolean", nullable=true)
   */
  private $hit;

  /**
   * @var bool
   *
   * @ORM\Column(name="is_novice", type="boolean", nullable=true)
   */
  private $novice;

  /**
   * @var bool
   *
   * @ORM\Column(name="is_with_gift", type="boolean")
   */
  private $withGift = false;

  /**
   * @var string
   *
   * @ORM\Column(length=255)
   * @Assert\NotNull(message = "Поле не может быть пустым.");
   */
  private $sku;

  /**
   * @var bool
   *
   * @ORM\Column(name="is_publication_allowed", type="boolean")
   */
  private $publicationAllowed = true;

  /**
   * @var bool
   *
   * @ORM\Column(type="boolean")
   */
  private $published = false;

  /**
   * @var float
   *
   * @ORM\Column(type="float", nullable=true)
   */
  private $weight;

  /**
   * @var float
   *
   * @ORM\Column(type="float", nullable=true)
   */
  private $length;

  /**
   * @var float
   *
   * @ORM\Column(type="float", nullable=true)
   */
  private $width;

  /**
   * @var float
   *
   * @ORM\Column(type="float", nullable=true)
   */
  private $height;

  /**
   * @var float
   *
   * @ORM\Column(type="float", nullable=true)
   */
  private $volume;

  /**
   * @var string
   *
   * @ORM\Column(length=16, nullable=true)
   */
  private $units;

  /**
   * @var boolean
   *
   * @ORM\Column(type="boolean", nullable=true)
   */
  private $isPurchasable = true;

  /**
   * @var float
   *
   * @ORM\Column(type="decimal", scale=2, nullable=true)
   */
  private $purchasePrice;

  /**
   * @var \DateTime
   *
   * @Gedmo\Timestampable(on="create")
   * @ORM\Column(type="datetime")
   */
  private $createdAt;

  /**
   * @var ArrayCollection
   *
   * @ORM\ManyToMany(targetEntity="StoreBundle\Entity\Store\Catalog\Taxonomy\Taxon", inversedBy="products", cascade={"persist"})
   * @ORM\JoinTable(name="products_to_taxons")
   */
  private $taxons;

  /**
   * @var ArrayCollection
   *
   * @ORM\OneToMany(targetEntity="StoreBundle\Entity\Store\Catalog\Product\ProductImage", mappedBy="product", cascade={"remove", "persist"})
   * @ORM\OrderBy({"position" = "ASC"})
   */
  private $images;

  /**
   * @var ArrayCollection
   *
   * @ORM\OneToMany(targetEntity="StoreBundle\Entity\Store\Order\OrderItem", mappedBy="product")
   */
  private $orderItems;


  private $sphinxWeight;

  /**
   * @var ProductType
   * @ORM\ManyToOne(targetEntity="StoreBundle\Entity\Store\Catalog\Product\Attributes\Type\ProductType", inversedBy="products")
   */
  private $productType;

  /**
   * @var ArrayCollection|ProductAttributeValue[]
   * @ORM\ManyToMany(targetEntity="StoreBundle\Entity\Store\Catalog\Product\Attributes\ProductAttributeValue", inversedBy="products", cascade={"persist"})
   * @ORM\JoinTable(name="product_attribute_values_to_products",
   *      joinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="id")},
   *      inverseJoinColumns={@ORM\JoinColumn(name="product_attribute_value_id", referencedColumnName="id")}
   * )
   */
  private $productAttributeValues;

  /**
   * @var integer
   * @ORM\Column(type="integer", nullable=false)
   */
  private $totalStock=0;

  /**
   * @var integer
   * @ORM\Column(type="integer", nullable=false)
   */
  private $reservedStock=0;

  /**
   * @var ProductStock[]|ArrayCollection
   * @ORM\OneToMany(targetEntity="StoreBundle\Entity\Store\Logistics\Warehouse\ProductStock",mappedBy="product", cascade={"persist"})
   * @LogisticAssert\OneWarehouseStock(message="В соответствии с текущими правилами работы магазина товар может находиться только на одном складе. Пожалуйста, укажите остаток товара только на одном складе, и очистите данные о наличии на остальных складах.")
   */
  private $stocks;

  /**
   * Бесплатная доставка
   * @var boolean
   * @ORM\Column(name="is_free_delivery", type="boolean")
   */
  private $is_free_delivery = false;

  /**
   * @var integer
   * @ORM\Column(type="float")
   */
  private $rank=0;

  /**
   * @var ProductRank
   * @ORM\OneToOne(targetEntity="StoreBundle\Entity\Store\Catalog\Product\ProductRank", mappedBy="product", cascade={"persist"})
   */
  private $productRank;
  
  /**
   * Внешний код товара в Моём Складе
   *
   * @var string
   * @ORM\Column(type="string", unique=true)
   */
  private $externalCode;
  
  /**
   * @var float
   * @ORM\Column(type="decimal", scale=2)
   */
  private $wholesalePrice;
  
  /**
   * @var string
   * @ORM\Column(type="integer", length=64, nullable=true)
   */
  private $background;
  
  /**
   * @var ArrayCollection
   * @ORM\ManyToMany(targetEntity="StoreBundle\Entity\Store\Catalog\Product\Product", inversedBy="id")
   */
  private $relatedProducts;

  /**
   * Исп. для цены за единицу
   * @var float
   * @ORM\Column(type="decimal", scale=3, nullable=false, options={"default": 1})
   */
  private $package;
  
  /**
   * Вес условной единицы (в упаковке, штуке и тд)
   * @var float
   * @ORM\Column(type="decimal", scale=3, nullable=false, options={"default": 1})
   */
  private $unitWeight;
  
  /**
   * @var string|null
   * @ORM\Column(name="image", length=255, nullable=true)
   * @Image(id="product/teaser")
   */
  private $teaserImageFile;
  
  /**
   * Является лди товар составным на стороне моего скалада
   * @var boolean
   * @ORM\Column(type="boolean", options={"default": 0})
   */
  private $bundle;
  
  /**
   * @var array|null
   * @ORM\Column(type="json_array", nullable=true)
   */
  private $teaserImageOptions;
  
  /**
   * @var int|null
   * @ORM\Column(type="integer", nullable=false, options={"default": 1})
   */
  private $multiplier;
  
  public function __construct()
  {
    $this->taxons = new ArrayCollection();
    $this->images = new ArrayCollection();
    $this->orderItems = new ArrayCollection();
    $this->relatedProducts= new ArrayCollection();

    $this->productAttributeValues = new ArrayCollection();
    $this->stocks = new ArrayCollection();
  }

  /**
   * Это используется внутри админки. Возвращает временно хранимые, переданные из формы значения.
   *
   * @return ArrayCollection|ProductAttributeValue[]
   */
  public function getProductAttributeValues()
  {
    return $this->productAttributeValues;
  }
  
  /**
   * @return ProductAttribute[]
   */
  public function getProductAttributeValuesGrouped()
  {
    $result = [];
    $attrs = [];
    if ($this->getProductAttributeValues()->count() === 0) return $result;
  
    foreach ($this->productAttributeValues as $value)
    {
      if($value->getProductAttribute()->getShowInProduct() === true)
      {
        $attrs[$value->getProductAttribute()->getName()] = $value->getProductAttribute()->getPosition();
      }
    }
    
    ksort($attrs);
    
    foreach ($this->productAttributeValues as $value)
    {
      if($value->getProductAttribute()->getShowInProduct() === true)
      {
        $result[$value->getProductAttribute()->getName()][] = $value->getValue();
      }
    }
    
    foreach ($result as $key=>$val)
    {
      $attrs[$key] = $val;
    }

    
    return $attrs;
  }

  /**
   * @param ArrayCollection|ProductAttributeValue[] $values
   * @return $this
   */
  public function setProductAttributeValues($values)
  {
    $this->productAttributeValues = $values;
    return $this;

  }

  /**
   * @param ProductAttributeValue $value
   * @return Product
   */
  public function addProductAttributeValue(ProductAttributeValue $value)
  {
    $this->productAttributeValues->add($value);
    return $this;
  }

  /**
   * @return int
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @param int $id
   * @return Product
   */
  public function setId($id)
  {
    $this->id = $id;

    return $this;
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
   * @return Product
   */
  public function setSlug($slug)
  {
    $this->slug = $slug;

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
   * @return Product
   */
  public function setName($name)
  {
    $this->name = $name;

    return $this;
  }

  /**
   * @return string
   */
  public function getShortDescription()
  {
    return $this->shortDescription;
  }

  /**
   * @param string $shortDescription
   * @return Product
   */
  public function setShortDescription($shortDescription)
  {
    $this->shortDescription = $shortDescription;

    return $this;
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
   * @return Product
   */
  public function setDescription($description)
  {
    $this->description = $description;

    return $this;
  }

  /**
   * @return float
   */
  public function getPrice()
  {
    return $this->price;
  }

  /**
   * @param float $price
   * @return Product
   */
  public function setPrice($price)
  {
    if (null === $price)
    {
      $this->price = null;
    }
    else
    {
      $this->price = (float)$price;
    }


    return $this;
  }

  /**
   * @return float
   */
  public function getOldPrice()
  {
    return $this->oldPrice;
  }

  /**
   * @param float $oldPrice
   * @return Product
   */
  public function setOldPrice($oldPrice)
  {
    $this->oldPrice = $oldPrice;

    return $this;
  }

  /**
   * Возвращает значение столбца "sale" для возможности администрирования
   *
   * @return bool
   */
  public function isSaleManual()
  {
    return $this->sale;
  }

  public function setSaleManual($v)
  {
    $this->setSale($v);
  }

  /**
   * Возвращает true, если товар имеет флаг "распродажа". Флаг может быть установлен вручную или автоматически.
   *
   * @return bool
   */
  public function isSale()
  {
    $isSale = $this->sale;

    if (null === $isSale)
    {
      $isSale = $this->getOldPrice() > $this->getPrice();
    }

    return $isSale;
  }

  /**
   * Устанавливает или снимает флаг "распродажа". Если передать null, состояние флага будет рассчитано автоматически.
   *
   * @param bool $sale
   * @return Product
   */
  public function setSale($sale)
  {
    $this->sale = $sale;

    return $this;
  }

  /**
   * @return bool
   */
  public function isHit()
  {
    return $this->hit;
  }

  /**
   * @param bool $hit
   * @return Product
   */
  public function setHit($hit)
  {
    $this->hit = $hit;

    return $this;
  }

  /**
   * @return bool
   */
  public function isNovice()
  {
    $novice = $this->novice;

    return $novice;
  }

  /**
   * @param bool $novice
   * @return Product
   */
  public function setNovice($novice)
  {
    $this->novice = $novice;

    return $this;
  }

  /**
   * @return string
   */
  public function getSku()
  {
    return $this->sku;
  }

  /**
   * @param string $sku
   * @return Product
   */
  public function setSku($sku)
  {
    $this->sku = $sku;

    return $this;
  }

  /**
   * @return bool
   */
  public function isPublicationAllowed()
  {
    return $this->publicationAllowed;
  }

  /**
   * @param bool $publicationAllowed
   * @return Product
   */
  public function setPublicationAllowed($publicationAllowed)
  {
    $this->publicationAllowed = $publicationAllowed;

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
   * @return Product
   */
  public function setPublished($published)
  {
    $this->published = $published;

    return $this;
  }

  /**
   * @return float
   */
  public function getWeight()
  {
    return $this->weight;
  }

  /**
   * @param float $weight
   * @return Product
   */
  public function setWeight($weight)
  {
    $this->weight = $weight;

    return $this;
  }

  /**
   * @return float
   */
  public function getLength()
  {
    return $this->length;
  }

  /**
   * @param float $length
   * @return Product
   */
  public function setLength($length)
  {
    $this->length = $length;

    return $this;
  }

  /**
   * @return float
   */
  public function getWidth()
  {
    return $this->width;
  }

  /**
   * @param float $width
   * @return Product
   */
  public function setWidth($width)
  {
    $this->width = $width;

    return $this;
  }

  /**
   * @return float
   */
  public function getHeight()
  {
    return $this->height;
  }

  /**
   * @param float $height
   * @return Product
   */
  public function setHeight($height)
  {
    $this->height = $height;

    return $this;
  }

  /**
   * @return float
   */
  public function getVolume()
  {
    return $this->volume;
  }

  /**
   * @param float $volume
   * @return Product
   */
  public function setVolume($volume)
  {
    $this->volume = $volume;

    return $this;
  }



  /**
   * @return string
   */
  public function getUnits()
  {
    return $this->units;
  }

  /**
   * @param string $units
   * @return Product
   */
  public function setUnits($units)
  {
    $this->units = $units;

    return $this;
  }

  public function getTaxons()
  {
    return $this->taxons;
  }

  public function setTaxons(ArrayCollection $taxons=null)
  {
    $this->taxons = $taxons;
  }

  public function addTaxon(Taxon $taxon)
  {
    if (!$this->taxons->contains($taxon))
    {
      $this->taxons->add($taxon);
    }
    return $this;
  }

  /**
   * @return ProductImage|null
   */
  public function getMainImage()
  {
    $criteria = Criteria::create();

    $criteria->orderBy(array('position' => Criteria::ASC))
             ->setMaxResults(1);

    $images =  $this->images->matching($criteria);

    if (!count($images))
    {
      return null;
    }

    return $images->first();
  }

  /**
   * @return ArrayCollection
   */
  public function getImages()
  {
    return $this->images;
  }

  /**
   * @param ArrayCollection $images
   * @return $this
   */
  public function setImages(ArrayCollection $images)
  {
    $this->images = $images;
    return $this;
  }

  /**
   * @param ProductImage $image
   * @return $this
   */
  public function addImage(ProductImage $image)
  {
    $this->images[] = $image;
    $image->setProduct($this);
    return $this;
  }

  /**
   * @return bool
   */
  public function isPurchasable()
  {
    //return $this->isPurchasable;
    return $this->isPublished();
  }

  /**
   * @param bool $isPurchasable
   * @return Product
   */
//  public function setIsPurchasable($isPurchasable)
//  {
//    $this->isPurchasable = $isPurchasable;
//    return $this;
//  }

  public function setSphinxWeight($v)
  {
    $this->sphinxWeight = $v;
  }

  public function getSphinxWeight()
  {
    return $this->sphinxWeight;
  }

  public function compare(Product $_b)
  {
    if ($this->getRank() == $_b->getRank())
    {
      return 0;
    }

    return ($this->getRank() > $_b->getRank()) ? -1 : 1;
  }

  public function getRank()
  {
    return $this->rank;
  }

  function __toString()
  {
    /** $this->getName()  */
    return (string)$this->getSku();
  }

  /**
   * @return ProductType
   */
  public function getProductType()
  {
    return $this->productType;
  }

  /**
   * @param ProductType
   */
  public function setProductType($productType)
  {
    $this->productType = $productType;
  }

  /**
   * @return Brand
   */
  public function getBrand()
  {
    return $this->brand;
  }

  /**
   * @param Brand $brand
   */
  public function setBrand($brand)
  {
    $this->brand = $brand;
  }

  /**
   * @return float
   */
  public function getPurchasePrice()
  {
    return $this->purchasePrice;
  }

  /**
   * @param float $purchasePrice
   */
  public function setPurchasePrice($purchasePrice)
  {
    $this->purchasePrice = $purchasePrice;
  }

  /**
   * Возвращает основной раздел товара
   *
   * @return Taxon
   */
  public function getPrimaryTaxon()
  {
    $taxons = $this->getTaxons();

    return $taxons->isEmpty() ? null : $taxons->first();
  }

  /**
   * @return bool
   */
  public function isWithGift()
  {
    return $this->withGift;
  }

  /**
   * @param bool $withGift
   */
  public function setWithGift($withGift)
  {
    $this->withGift = $withGift;
  }

  /**
   * @return \DateTime
   */
  public function getCreatedAt()
  {
    return $this->createdAt;
  }

  public function getSlugSource()
  {
    return $this->getName();
  }

  /**
   * @ORM\PrePersist()
   */
  public function setNovicePrePersist()
  {
    /** Если при создании товара не указано что это новинка, то автоматич. становится новикой */
    if(is_null($this->isNovice()))
      $this->setNovice(true);
  }

  /**
   * @return bool
   */
  public function isFreeDelivery ()
  {
    return $this->is_free_delivery;
  }

  /**
   * @param bool $is_free_delivery
   * @return Product
   */
  public function setIsFreeDelivery ($is_free_delivery)
  {
    $this->is_free_delivery = $is_free_delivery;
    return $this;
  }




  public function getFirstImage()
  {
    /** @var MediaInterface $image */
    $image = $this->getImages()->first();

    return (!$image) ? null : (string)'/uploads/'.$image->getResourceId();
  }



  public function __clone ()
  {
    if ($this->id)
    {
      $this->setId(null);
      $this->setName($this->getName().' копия');
      $this->setSlug(null);

      $images = $this->getImages();
      $cloned_images = new ArrayCollection();

      foreach ($images as $image)
      {
        $cloned_image = clone $image;
        $cloned_image->setProduct($this);
        $cloned_images->add($cloned_image);
      }

      $this->setImages($cloned_images);
    }
  }

  /**
   * @return float|int
   */
  public function getDiscountLevel()
  {
    if (!$this->getPrice())
    {
      return 0;
    }

    return round((1 - $this->getOldPrice() / $this->getPrice()) * 100);
  }

  public function setTotalStock ($stock = null)
  {
    $this->totalStock = $stock;
    return $this;
  }

  public function getTotalStock ()
  {
    return $this->totalStock;
  }

  public function setInStock ($isInStock)
  {
    return $this;
  }

  public function getInStock ()
  {
    return $this->totalStock > 0;
  }

  /**
   * @return ArrayCollection|ProductStock[]
   */
  public function getStocks ()
  {
    return $this->stocks;
  }

  /**
   * @param ArrayCollection|ProductStock[] $stocks
   * @return $this
   */
  public function setStocks ($stocks)
  {
    foreach ($stocks as $stock)
    {
      $this->addStock($stock);
    }

    return $this;
  }

  /**
   * @param ProductStockInterface|ProductStock $stock
   * @return $this
   */
  public function addStock (ProductStockInterface $stock)
  {
    $this->stocks->add($stock);
    $stock->setProduct($this);
    return $this;
  }

  /**
   * @param ProductStockInterface $stock
   * @return $this
   */
  public function removeStock (ProductStockInterface $stock)
  {
    $this->stocks->removeElement($stock);
    return $this;
  }

  /**
   * @inheritdoc
   */
  public function setReservedStock ($stock = null)
  {
    $this->reservedStock = $stock;
    return $this;
  }

  /**
   * @inheritdoc
   */
  public function getReservedStock ()
  {
    return $this->reservedStock;
  }

  /**
   * @inheritdoc
   */
  public function getAvailableStock ()
  {
    return ($this->totalStock - $this->reservedStock) > 0 ? ($this->totalStock - $this->reservedStock) : 0;
  }

  public function setRank($rank)
  {
    $this->rank = $rank;
    return $this;
  }

  /**
   * @return ProductRank
   */
  public function getProductRank ()
  {
    if (!$this->productRank)
    {
      $this->productRank = new ProductRank();
      $this->productRank->setProduct($this);
    }

    return $this->productRank;
  }
  
  /**
   * @return string
   */
  public function getExternalCode(): string
  {
    return $this->externalCode;
  }
  
  /**
   * @param string $externalCode
   */
  public function setExternalCode(string $externalCode): void
  {
    $this->externalCode = $externalCode;
  }
  
  /**
   * @return float
   */
  public function getWholesalePrice()
  {
    return $this->wholesalePrice;
  }
  
  /**
   * @param float $wholesalePrice
   */
  public function setWholesalePrice($wholesalePrice)
  {
    if (null === $wholesalePrice)
    {
      $this->wholesalePrice = null;
    }
    else
    {
      $this->wholesalePrice = (float)$wholesalePrice;
    }
  }
  
  /**
   * @return int
   */
  public function getBackground(): ?int
  {
    return $this->background;
  }
  
  /**
   * @param int $background
   */
  public function setBackground(?int $background): void
  {
    $this->background = $background;
  }
  
  /**
   * @return ArrayCollection
   */
  public function getRelatedProducts()
  {
    return $this->relatedProducts;
  }
  
  /**
   * @param ArrayCollection $relatedProducts
   */
  public function setRelatedProducts(ArrayCollection $relatedProducts): void
  {
    $this->relatedProducts = $relatedProducts;
  }

  /**
   * @return integer
   */
  public function getPackage()
  {
    return $this->package;
  }
  
  public function getFormattedPackage()
  {
    return $this->formatFloat($this->getPackage());
  }

  /**
   * @param integer $package
   */
  public function setPackage($package)
  {
    $this->package = $package;
  }

  /**
   * @return bool
   */
  public function getMeasured()
  {
    if ($this->getProductType())
    {
      return $this->getProductType()->getMeasured();
    }

    return false;
  }

  /**
   * @return mixed
   */
  public function getMinCount()
  {
    $productType = $this->getProductType();

    if ($productType)
    {
      return $this->formatFloat($productType->getMinCount());
    }

    return 1;
  }

  /**
   * @return mixed
   */
  public function getCountStep()
  {
    $productType = $this->getProductType();

    if ($productType)
    {
      return $this->formatFloat($productType->getCountStep());
    }

    return 1;
  }

  /**
   * Убирает нули после запятой, если число целое.
   *
   * @param $number
   * @return string
   */
  private function formatFloat($number)
  {
    if ($number - floor($number) == 0)
    {
      return rtrim(rtrim($number, '0'), '.');
    }

    return $number;
  }
  
  
  /**
   * @param null $id
   * @return ImageInterface|null|\StoreBundle\Media\Store\Catalog\Product\ProductImage
   */
  public function getImage($id = null)
  {
    if (!$this->teaserImageFile)
    {
      return null;
    }
    
    return new \StoreBundle\Media\Store\Catalog\Product\ProductImage('teaser', $this->teaserImageFile, $this->getTeaserImageOptions());
  }
  
  /**
   * @param ImageInterface $teaser
   * @return mixed|void
   */
  public function setImage(ImageInterface $teaser)
  {
    $this->teaserImageFile = $teaser ? $teaser->getResourceId() : null;
  }
  
  /**
   * @param $id
   * @return mixed
   */
  public function getImageOptions($id)
  {
    return $this->getTeaserImageOptions();
  }
  
  public function setImageOptions($id)
  {
    $this->setTeaserImageOptions($id);
  }
  
  /**
   * @return string
   */
  public function getTeaserImageFile()
  {
    return $this->teaserImageFile;
  }
  
  /**
   * @param string $teaserImageFile
   * @return $this
   */
  public function setTeaserImageFile($teaserImageFile)
  {
    /*
     * Не даем сбрасывать изображение из-за пустого значения в форме
     */
    if (null !== $teaserImageFile)
    {
      $this->teaserImageFile = $teaserImageFile;
    }
    
    return $this;
  }
  
  /**
   * @return ImageInterface | null
   */
  public function getTeaserImageFileImage()
  {
    if (null == $this->teaserImageFile)
    {
      return null;
    }
    
    return new \StoreBundle\Media\Store\Catalog\Product\ProductImage('product/teaser', $this->teaserImageFile, []);
  }
  
  public function setTeaserImageFileImage(ImageInterface $image = null)
  {
    $this->setTeaserImageFile($image ? $image->getResourceId() : null);
  }
  
  /**
   * @return array
   */
  public function getTeaserImageOptions()
  {
    return $this->teaserImageOptions;
  }
  
  /**
   * @param array $teaserImageOptions
   * @return $this
   */
  public function setTeaserImageOptions($teaserImageOptions)
  {
    $this->teaserImageOptions = $teaserImageOptions;
    return $this;
  }
  
  public function getThumbnailUrl($alias = '')
  {
    $image = $this->getTeaserImageFileImage();
    $thumbnail = $image === null ? null : new ImageThumbnail($alias, $image);
  
    return $thumbnail ? '/uploads/' . $thumbnail->getResourceId() : null;
  }
  
  /**
   * @return bool
   */
  public function isBundle(): ?bool
  {
    return $this->bundle;
  }
  
  /**
   * @param bool $bundle
   */
  public function setBundle(?bool $bundle): void
  {
    $this->bundle = $bundle;
  }
  
  /**
   * @return ArrayCollection
   */
  public function getOrderItems(): ArrayCollection
  {
    return $this->orderItems;
  }
  
  /**
   * @param ArrayCollection $orderItems
   */
  public function setOrderItems(ArrayCollection $orderItems): void
  {
    $this->orderItems = $orderItems;
  }
  
  /**
   * @return int|null
   */
  public function getMultiplier(): ?int
  {
    return $this->multiplier;
  }
  
  /**
   * @param int|null $multiplier
   */
  public function setMultiplier(?int $multiplier): void
  {
    $this->multiplier = $multiplier;
  }
  
  /**
   * @return float
   */
  public function getUnitWeight(): ?float
  {
    return $this->unitWeight;
  }
  
  /**
   * @param float $unitWeight
   */
  public function setUnitWeight(?float $unitWeight): void
  {
    $this->unitWeight = $unitWeight;
  }
  
  /**
   * Возращает цену за еденицу товара, исходя из множителя веса
   * @return float
   */
  public function getUnitPrice()
  {
    if($this->getMeasured()) {
      return $this->getPrice();
    }
    return $this->getPrice() / $this->getMultiplier() * $this->getUnitWeight();
  }
  
  /**
   * Служит только для того, чтобы вывести цену за весовой товар
   * TODO переделать по-человечески в PriceManager
   * @return float|int
   */
  function getMeasuredPartPrice()
  {
    return $this->getPrice() / $this->getMultiplier() * $this->getUnitWeight();
  }
  
}