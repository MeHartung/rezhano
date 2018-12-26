<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Entity\Store\Catalog\Product;

use Accurateweb\ImagingBundle\Filter\CropFilterOptionsResolver;
use Accurateweb\ImagingBundle\Filter\FilterChain;
use Accurateweb\MediaBundle\Model\Image\ImageAwareInterface;
use Accurateweb\MediaBundle\Model\Media\ImageInterface;
use Accurateweb\MediaBundle\Model\Media\MediaCroppableInterface;
use Accurateweb\MediaBundle\Model\Media\MediaInterface;
use Accurateweb\MediaBundle\Model\Thumbnail\ImageThumbnail;
use Accurateweb\MediaBundle\Model\Thumbnail\ThumbnailDefinition;
use App\Media\Gallery\ProductPhoto\ProductPhotoImage;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * Изображение товара
 *
 * @package StoreBundle\Entity\Store\Catalog\Product
 * @ORM\Table(name="product_images")
 * @ORM\Entity(repositoryClass="StoreBundle\Repository\Store\Catalog\Product\ProductImageRepository")
 */
class ProductImage implements ImageInterface, ImageAwareInterface, MediaCroppableInterface
{
  /**
   * @var int
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue
   */
  private $id;

  /**
   * @var int
   *
   * @ORM\Column(type="integer", nullable=true)
   */
  private $virtuemartMediaId;

  /**
   * @var string
   *
   * @ORM\Column(length=512)
   */
  private $filename;

  /**
   * @Gedmo\SortablePosition
   * @ORM\Column(type="integer")
   */
  private $position;

  /**
   * @Gedmo\SortableGroup
   * @ORM\ManyToOne(targetEntity="Product", inversedBy="images")
   * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
   */
  private $product;

  /**
   * @var array|null
   * @ORM\Column(type="simple_array", nullable=true)
   */
  private $crop;

  /**
   * @return int
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @param int $id
   * @return ProductImage
   */
  public function setId($id)
  {
    $this->id = $id;

    return $this;
  }

  /**
   * @return string
   */
  public function getFilename()
  {
    return $this->filename;
  }

  /**
   * @param mixed $filename
   * @return ProductImage
   */
  public function setFilename($filename)
  {
    $this->filename = $filename;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getPosition()
  {
    return $this->position;
  }

  /**
   * @param mixed $position
   * @return ProductImage
   */
  public function setPosition($position)
  {
    $this->position = $position;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getProduct()
  {
    return $this->product;
  }

  /**
   * @param mixed $product
   * @return ProductImage
   */
  public function setProduct($product)
  {
    $this->product = $product;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getVirtuemartMediaId()
  {
    return $this->virtuemartMediaId;
  }

  /**
   * @param mixed $virtuemartMediaId
   * @return ProductImage
   */
  public function setVirtuemartMediaId($virtuemartMediaId)
  {
    $this->virtuemartMediaId = $virtuemartMediaId;
    return $this;
  }

  /**
   * @return string
   */
  public function getResourceId()
  {
    return $this->getFilename();
  }

  /**
   * @param $resourceId string
   */
  public function setResourceId($resourceId)
  {
    $this->setFilename($resourceId);
  }

  /**
   * @return array
   */
  public function getThumbnailDefinitions()
  {
    $cropResolverOptions = [
      'auto_crop' => true,
      'auto_crop_aspect_ratio' => 1,
      'auto_crop_position' => 'center'
    ];
    $cropOptions = [];

    if ($this->crop && !is_null($this->crop[0]))
    {
      $cropResolverOptions = [
        'auto_crop' => false,
      ];

      $cropOptions = [
        'left' => $this->crop[0],
        'top' => $this->crop[1],
        'width' => $this->crop[2] - $this->crop[0],
        'height' => $this->crop[3] - $this->crop[1],
      ];
    }
    return array(
//      new ThumbnailDefinition('preview', new FilterChain(array(
//        array(
//          'id' => 'crop',
//          'options' => $this->getCrop(),
//          'resolver' => new CropFilterOptionsResolver()),
//        array('id' => 'resize', 'options' => array('size' => '80x80'))
//      ))),
//      new ThumbnailDefinition('160x160', new FilterChain(array(
//        array('id' => 'resize', 'options' => array('size' => '160x160'))
//      ))),
//      new ThumbnailDefinition('50x50', new FilterChain(array(
//        array(
//          'id' => 'crop',
//          'options' => $this->getCrop(),
//          'resolver' => new CropFilterOptionsResolver()),
//        array('id' => 'resize', 'options' => array('size' => '50x50'))
//      ))),
      new ThumbnailDefinition('570x713', new FilterChain(array(
        array(
          'id' => 'crop',
          'options' => $cropOptions,
          'resolver' => new CropFilterOptionsResolver($cropResolverOptions),
        ),
//        array('id' => 'resize', 'options' => array('size' => '570x713'))
      ))),
    );
  }

  public function getThumbnail($id)
  {
    $definitions = $this->getThumbnailDefinitions();

    $found = false;
    foreach ($definitions as $definition)
    {
      if ($definition->getId() == $id)
      {
        $found = true;
        break;
      }
    }

    if (!$found)
    {
      throw new \Exception('Image thumbnail definition not found');
    }

    return new ImageThumbnail($id, $this);
  }

  /**
   * @param $id
   * @return ImageInterface
   */
  public function getImage($id = null)
  {
    return $this;
  }

  /**
   * @param ImageInterface $image
   * @return ProductImage
   */
  public function setImage(ImageInterface $image)
  {
    $this->setResourceId($image->getResourceId());
    return $this;
  }

  /**
   * @param $id
   * @return mixed
   */
  public function getImageOptions($id)
  {
    return null;
  }

  public function setImageOptions($id)
  {
    // TODO: Implement setImageOptions() method.
  }

  /**
   * @return array|null
   */
  public function getCrop ()
  {
    if (!$this->crop || count($this->crop) < 4)
    {
      return array(null, null, null, null);
    }
    return $this->crop;
  }

  /**
   * @param array|null $crop
   * @return $this
   */
  public function setCrop ($crop)
  {
    $this->crop = $crop;
    return $this;
  }
}