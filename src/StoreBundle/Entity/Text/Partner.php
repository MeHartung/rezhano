<?php
/**
 * Created by PhpStorm.
 * User: eobuh
 * Date: 24.10.2018
 * Time: 19:00
 */

namespace StoreBundle\Entity\Text;


use Accurateweb\MediaBundle\Annotation\Image;
use Accurateweb\MediaBundle\Model\Image\ImageAwareInterface;
use Accurateweb\MediaBundle\Model\Media\ImageInterface;
use Accurateweb\MediaBundle\Model\Thumbnail\ImageThumbnail;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use StoreBundle\Media\Text\PartnerImage;
use StoreBundle\Media\Text\UnprocessedImage;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Partner
 * @ORM\Table()
 * @ORM\Entity()
 */
class Partner implements ImageAwareInterface
{
  /**
   * @var integer|null
   * @ORM\Column(type="integer")
   * @ORM\Id()
   * @ORM\GeneratedValue()
   */
  private $id;
  
  /**
   * @var string|null
   * @ORM\Column(type="string")
   * @Assert\NotNull(message="Поле «Имя» не может быть пустым!")
   */
  private $name;
  
  /**
   * @var int|null
   * @ORM\Column(type="integer")
   * @Gedmo\SortablePosition()
   */
  private $position;
  
  /**
   * @var string|null
   * @ORM\Column(name="image", length=255)
   * @Image(id="partner/teaser")
   */
  private $teaserImageFile;
  
  /**
   * @var array|null
   * @ORM\Column(type="json_array", nullable=true)
   */
  private $teaserImageOptions;
  
  /**
   * @param null $id
   * @return ImageInterface|null|PartnerImage
   */
  public function getImage($id = null)
  {
    if (!$this->teaserImageFile)
    {
      return null;
    }
    
    return new PartnerImage('teaser', $this->teaserImageFile, $this->getTeaserImageOptions());
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
   * @return News
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
    
    return new PartnerImage('partner/teaser', $this->teaserImageFile, []);
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
   * @return News
   */
  public function setTeaserImageOptions($teaserImageOptions)
  {
    $this->teaserImageOptions = $teaserImageOptions;
    return $this;
  }
  
  /**
   * @return int|null
   */
  public function getId(): ?int
  {
    return $this->id;
  }
  
  /**
   * @param int|null $id
   */
  public function setId(?int $id): void
  {
    $this->id = $id;
  }
  
  /**
   * @return null|string
   */
  public function getName(): ?string
  {
    return $this->name;
  }
  
  /**
   * @param null|string $name
   */
  public function setName(?string $name): void
  {
    $this->name = $name;
  }
  
  /**
   * @return int|null
   */
  public function getPosition(): ?int
  {
    return $this->position;
  }
  
  /**
   * @param int|null $position
   */
  public function setPosition(?int $position): void
  {
    $this->position = $position;
  }
  
  public function __toString()
  {
    return $this->getName() ? $this->getName() : '';
  }
  
  public function getThumbnailUrl($alias = '')
  {
    $image = $this->getTeaserImageFileImage();
    $thumbnail = $image === null ? null : new ImageThumbnail($alias, $image);
    
    return $thumbnail ? '/uploads/' . $thumbnail->getResourceId() : null;
  }
}