<?php


namespace StoreBundle\Entity\Text\About;

use Accurateweb\ImagingBundle\Filter\CropFilterOptionsResolver;
use Accurateweb\ImagingBundle\Filter\FilterChain;
use Accurateweb\MediaBundle\Model\Image\ImageAwareInterface;
use Accurateweb\MediaBundle\Model\Media\ImageInterface;
use Accurateweb\MediaBundle\Model\Media\MediaCroppableInterface;
use Accurateweb\MediaBundle\Model\Thumbnail\ImageThumbnail;
use Accurateweb\MediaBundle\Model\Thumbnail\ThumbnailDefinition;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="StoreBundle\Repository\Text\About\AboutUsImageRepository")
 * @ORM\Table(name="about_us_image")
 */
class AboutUsImage  implements ImageInterface, ImageAwareInterface, MediaCroppableInterface
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
   * @var string
   *
   * @ORM\Column(length=512)
   */
  private $filename;
  
  /**
   * @Gedmo\SortablePosition()
   * @ORM\Column(type="integer")
   */
  private $position;
  
  /**
   * @var array|null
   * @ORM\Column(type="simple_array", nullable=true)
   */
  private $crop;
  
  /**
   * @var AboutUsGallery
   * @ORM\ManyToOne(targetEntity="StoreBundle\Entity\Text\About\AboutUsGallery", inversedBy="images")
   * @ORM\JoinColumn(name="gallery_id", referencedColumnName="id")
   */
  private $gallery;
  
  /**
   * @return int
   */
  public function getId()
  {
    return $this->id;
  }
  
  /**
   * @param int $id
   * @return $this
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
   * @return $this
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
   * @return $this
   */
  public function setPosition($position)
  {
    $this->position = $position;
    
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
      new ThumbnailDefinition('preview', new FilterChain(array(
        array(
          'id' => 'crop',
          'options' => $this->getCrop(),
          'resolver' => new CropFilterOptionsResolver()),
        array('id' => 'resize', 'options' => array('size' => '80x80'))
      ))),
      new ThumbnailDefinition('160x160', new FilterChain(array(
        array('id' => 'resize', 'options' => array('size' => '160x160'))
      ))),
      new ThumbnailDefinition('50x50', new FilterChain(array(
        array(
          'id' => 'crop',
          'options' => $this->getCrop(),
          'resolver' => new CropFilterOptionsResolver()),
        array('id' => 'resize', 'options' => array('size' => '50x50'))
      ))),
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
   * @return $this
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
  
  /**
   * @return mixed
   */
  public function getGallery()
  {
    return $this->gallery;
  }
  
  /**
   * @param mixed $gallery
   */
  public function setGallery($gallery): void
  {
    $this->gallery = $gallery;
  }
}