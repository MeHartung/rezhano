<?php

namespace StoreBundle\Entity\Text;

use Accurateweb\MediaBundle\Annotation\Image;
use Accurateweb\MediaBundle\Model\Image\ImageAwareInterface;
use Accurateweb\MediaBundle\Model\Media\ImageInterface;
use StoreBundle\Media\Text\NewsImage;
use StoreBundle\Media\Text\UnprocessedImage;
use StoreBundle\Sluggable\SluggableInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * Новости
 *
 * @ORM\Table(name="news")
 * @ORM\Entity(repositoryClass="StoreBundle\Repository\Text\NewsRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("title")
 * @UniqueEntity("slug")
 *
 */
class News implements SluggableInterface, ImageAwareInterface
{
  /**
   * @var int
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(length=255, unique=true)
   *
   * @Assert\Regex(pattern="/^[a-z0-9_\-]+$/",
   *   message="Поле может содержать только буквы лат. алфавита, знак земли и подчеркивания")
   */
  private $slug;

  /**
   * @var string
   *
   * @ORM\Column(length=255, unique=true)
   * @Assert\NotNull(message = "Поле не может быть пустым.");
   * @Assert\Length(
   *   min = 1,
   *   max = 255,
   *   minMessage = "Заголовок не может быть короче {{ limit }} символа",
   *   maxMessage = "Заголовок не может быть длинее {{ limit }} символов"
   * )
   */
  private $title;

  /**
   * @var string
   *
   * @ORM\Column(length=1024)
   * @Assert\NotNull(message = "Поле не может быть пустым.");
   * @Assert\Length(
   *   min = 1,
   *   max = 1024,
   *   minMessage = "Анонс не может быть короче {{ limit }} символа",
   *   maxMessage = "Анонс не может быть длинее {{ limit }} символов"
   * )
   */
  private $announce;

  /**
   * @var string
   *
   * @ORM\Column(type="text")
   * @Assert\NotNull(message = "Поле не может быть пустым.");
   */
  private $text;

  /**
   * @var boolean
   *
   * @ORM\Column(type="boolean")
   */
  private $published = false;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="created_at", type="datetime")
   */
  private $createdAt;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="updated_at", type="datetime")
   */
  private $updatedAt;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="published_at", type="datetime", nullable=true)
   */
  private $publishedAt;

  /**
   * @var string
   *
   * @ORM\Column(name="image", length=255, nullable=true)
   * @Image(id="news/teaser")
   */
  private $teaserImageFile;

  /**
   * @var array
   *
   * @ORM\Column(type="json_array", nullable=true)
   */
  private $teaserImageOptions;

  /**
   * @var boolean
   */
  private $publishedUp;

  /**
   * Get id
   *
   * @return int
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }

  /**
   * @param string $title
   * @return News
   */
  public function setTitle($title)
  {
    $this->title = $title;

    return $this;
  }

  /**
   * @return string
   */
  public function getAnnounce()
  {
    return $this->announce;
  }

  /**
   * @param string $announce
   * @return News
   */
  public function setAnnounce($announce)
  {
    $this->announce = $announce;

    return $this;
  }

  /**
   * @return string
   */
  public function getText()
  {
    return $this->text;
  }

  /**
   * @param string $text
   * @return News
   */
  public function setText($text)
  {
    $this->text = $text;

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
   * @return News
   */
  public function setPublished($published)
  {
    $this->published = $published;
    return $this;
  }

  /**
   * @return \DateTime
   */
  public function getCreatedAt()
  {
    return $this->createdAt;
  }

  /**
   * @ORM\PrePersist
   * @param \DateTime $createdAt
   */
  public function updateCreatedAt($createdAt)
  {
    if (!$this->createdAt)
    {
      $this->setCreatedAt(new \DateTime());
    }
  }
  /**
   * @param \DateTime $createdAt
   *
   * @return News
   */
  public function setCreatedAt($createdAt)
  {
    $this->createdAt = $createdAt;
    return $this;
  }

  /**
   * @return \DateTime
   */
  public function getUpdatedAt()
  {
    return $this->updatedAt;
  }

  /**
   * @ORM\PrePersist()
   * @ORM\PreUpdate()
   * @return $this
   */
  public function updateUpdatedAt()
  {
    $this->setUpdatedAt(new \DateTime());
    return $this;
  }

  /**
   * @param \DateTime $updatedAt
   * @return News
   */
  public function setUpdatedAt($updatedAt)
  {
    $this->updatedAt = $updatedAt;
    return $this;
  }

  /**
   * @return \DateTime
   */
  public function getPublishedAt()
  {
    return $this->publishedAt;
  }

  /**
   * @return bool
   */
  public function getPublishedUp()
  {
    return $this->publishedUp;
  }
  /**
   * @ORM\PrePersist()
   * @ORM\PostLoad()
   * @param bool $published
   * @return $this
   */
  public function createPublishedUp($published){
   $this->publishedUp = $this->published;
   return $this;
  }

  /**
   * @ORM\PrePersist()
   * @return $this
   */
  public function createPublishedAt()
  {
    $this->setPublishedAt(new \DateTime());
    return $this;
  }

  /**
   * @ORM\PreUpdate()
   * @param \DateTime $publishedAt
   * @return News
   */
  public function updatePublishedAt($publishedAt)
  {
    if ($this->published == 1 && $this->publishedUp !== $this->published)
    {
      $this->setPublishedAt(new \DateTime());
    }

    return $this;
  }

  /**
   * @param \DateTime $publishedAt
   * @return News
   */
  public function setPublishedAt($publishedAt)
  {
    $this->publishedAt = $publishedAt;
    return $this;
  }

  /**
   * @return NewsImage
   */
  public function getImage($id=null)
  {
    if (!$this->teaserImageFile)
    {
      $matches = array();
      if (false === preg_match('/<img\s+src=["\']([^"\']+)["\']/', $this->announce, $matches))
      {
        return null;
      }

      if (isset($matches[1]))
      {
        $this->teaserImageFile = $matches[1];
      }
      else
      {
        return null;
      }
    }

    return new NewsImage('teaser', $this->teaserImageFile, $this->getTeaserImageOptions());
  }

  /**
   * @param NewsImage $teaser
   */
  public function setImage(ImageInterface $teaser)
  {
    $this->teaserImageFile = $teaser ? $teaser->getResourceId() : null;
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
    
    return new UnprocessedImage('homepage-promo-banner/teaser', $this->teaserImageFile, []);
  }
  
  public function setTeaserImageFileImage(ImageInterface $image = null)
  {
    $this->setTeaserImageFile($image ? $image->getResourceId() : null);
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
   */
  public function setSlug($slug)
  {
    $this->slug = $slug;
  }

  public function getSlugSource()
  {
    return $this->getTitle();
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


}

