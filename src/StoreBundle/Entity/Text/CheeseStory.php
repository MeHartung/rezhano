<?php

namespace StoreBundle\Entity\Text;

use Accurateweb\MediaBundle\Annotation\Image;
use Accurateweb\MediaBundle\Model\Image\ImageAwareInterface;
use Accurateweb\MediaBundle\Model\Media\ImageInterface;
use StoreBundle\Media\Text\CheeseStoryImage;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use StoreBundle\Media\Text\UnprocessedImage;
use StoreBundle\Sluggable\SluggableInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class CheeseStory
 * @ORM\Table(name="cheese_stories")
 * @ORM\Entity()
 * @UniqueEntity(fields={"slug"})
 */
class CheeseStory implements ImageAwareInterface, SluggableInterface
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
   * @ORM\Column(type="string", length=256)
   * @Assert\NotBlank(message="Поле не может быть пустым")
   * @Assert\Length(min="1", max="256",
   *   minMessage="Введите не менее 1 символа"),
   *   maxMessage="Введите не более 256 символов" )
   */
  private $title;
  
  /**
   * Текст в списке
   *
   * @var string|null
   * @ORM\Column(type="text")
   * @Assert\NotBlank(message="Поле не может быть пустым")
   */
  private $previewText;
  
  /**
   * @var string|null
   * @ORM\Column(type="text", nullable=true)
   * @ Assert\NotBlank(message="Поле не может быть пустым")
   */
  private $text;
  
  /**
   * @var string|null
   * @ORM\Column(type="string", name="photo", nullable=true)
   * @ Assert\NotBlank(message="Поле не может быть пустым")
   * @Image(id="cheese-story/teaser")
   */
  private $teaserImageFile;
  
  /**
   * @var integer|null
   * @ORM\Column(type="integer")
   * @Gedmo\SortablePosition()
   */
  private $position;
  
  /**
   * @var string|null
   * @ORM\Column(type="json_array", nullable=true)
   */
  private $teaserImageOptions;
  
  /**
   * @var boolean
   * @ORM\Column(type="boolean", options={"default": 1})
   */
  private $published;
  
  /**
   * @var string
   * @ORM\Column(type="string", unique=true)
   */
  private $slug;
  
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
  public function getText(): ?string
  {
    return $this->text;
  }
  
  /**
   * @param null|string $text
   */
  public function setText(?string $text): void
  {
    $this->text = $text;
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
    
    return new UnprocessedImage('cheese-story/teaser', $this->teaserImageFile, []);
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
   * @return Article
   */
  public function setTeaserImageOptions($teaserImageOptions)
  {
    $this->teaserImageOptions = $teaserImageOptions;
    
    return $this;
  }
  
  /**
   * @return CheeseStoryImage
   */
  public function getImage($id = null)
  {
    if (!$this->teaserImageFile)
    {
      return null;
    }
    
    return new CheeseStoryImage('teaser', $this->teaserImageFile, $this->getTeaserImageOptions());
  }
  
  /**
   * @param ArticleImage $teaser
   */
  public function setImage(ImageInterface $teaser)
  {
    $this->teaserImageFile = $teaser ? $teaser->getResourceId() : null;
  }
  
  /**
   * @return string
   */
  public function getTitle(): ?string
  {
    return $this->title;
  }
  
  /**
   * @param null|string $title
   */
  public function setTitle(?string $title): void
  {
    $this->title = $title;
  }
  
  /**
   * @return null|string
   */
  public function getPreviewText(): ?string
  {
    return $this->previewText;
  }
  
  /**
   * @param null|string $previewText
   */
  public function setPreviewText(?string $previewText): void
  {
    $this->previewText = $previewText;
  }
  
  /**
   * @return bool
   */
  public function isPublished(): ?bool
  {
    return $this->published;
  }
  
  /**
   * @param bool $published
   */
  public function setPublished(?bool $published): void
  {
    $this->published = $published;
  }
  
  /**
   * @return string
   */
  public function getSlug(): ?string
  {
    return $this->slug;
  }
  
  /**
   * @param string $slug
   */
  public function setSlug($slug): void
  {
    $this->slug = $slug;
  }
  
  public function getSlugSource()
  {
    return $this->getTitle();
  }
  
  public function __toString()
  {
    return $this->getTitle() === null ? '' : $this->getTitle();
  }
}