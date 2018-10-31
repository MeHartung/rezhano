<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Entity\Text;


use Accurateweb\MediaBundle\Annotation\Image;
use Accurateweb\MediaBundle\Model\Image\ImageAwareInterface;
use Accurateweb\MediaBundle\Model\Media\ImageInterface;
use StoreBundle\Media\Text\ArticleImage;
use StoreBundle\Media\Text\UnprocessedImage;
use StoreBundle\Sluggable\SluggableInterface;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * Статьи
 *
 * @ORM\Table(name="articles")
 * @ORM\Entity(repositoryClass="StoreBundle\Repository\Text\ArticleRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("title")
 * @UniqueEntity("slug")
 */
class Article implements SluggableInterface, ImageAwareInterface

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
   *
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
   * @Assert\NotBlank()
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
   * @var string
   *
   * @ORM\Column(name="image", length=255, nullable=true)
   * @Image(id="article/news")
   */
  private $teaserImageFile;

  /**
   * @var array
   *
   * @ORM\Column(type="json_array", nullable=true)
   */
  private $teaserImageOptions;

  /**
   * @return int
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @param int $id
   * @return Article
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
   * @return Article
   */
  public function setSlug($slug)
  {
    $this->slug = $slug;

    return $this;
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
   * @return Article
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
   * @return Article
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
   * @return Article
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
   * @return Article
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
   * @return Article
   *
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
   * @param  \DateTime $updatedAt
   * @return Article
   */
  public function setUpdatedAt($updatedAt)
  {
    $this->updatedAt = $updatedAt;
    return $this;
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
   * @return Article
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
    
    return new UnprocessedImage('article/teaser', $this->teaserImageFile, []);
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
   * @return ArticleImage
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

    return new ArticleImage('teaser', $this->teaserImageFile, $this->getTeaserImageOptions());
  }

  /**
   * @param ArticleImage $teaser
   */
  public function setImage(ImageInterface $teaser)
  {
    $this->teaserImageFile = $teaser ? $teaser->getResourceId() : null;
  }

}