<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 04.08.17
 * Time: 11:32
 */

namespace StoreBundle\Entity\Text;

use Accurateweb\MediaBundle\Annotation\Image;
use Accurateweb\MediaBundle\Model\Image\ImageAwareInterface;
use Accurateweb\MediaBundle\Model\Media\ImageInterface;
use Doctrine\ORM\Mapping as ORM;
use StoreBundle\Media\Text\SpecialOfferImage;
use StoreBundle\Media\Text\UnprocessedImage;
use StoreBundle\Sluggable\SluggableInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Акции
 *
 * @ORM\Table(name="special_offers")
 * @ORM\Entity(repositoryClass="StoreBundle\Repository\Text\SpecialOfferRepository")
 * @ORM\HasLifecycleCallbacks()
 *
 * @UniqueEntity("title")
 * @UniqueEntity("slug")
 *
 */
class SpecialOffer implements SluggableInterface, ImageAwareInterface
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
   * @Assert\Regex (pattern="/^[a-z0-9_\-]+$/",
   *   message="Поле может содержать только буквы лат. алфавита, знак земли и подчеркивания")
   *
   */
  private $slug;

  /**
   * @var string
   *
   * @ORM\Column(length=255, unique=true)
   * @Assert\NotNull(message = "Поле не может быть пустым.");
   *
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
   * @var \DateTime
   *
   * @ORM\Column(name="date_start", type="datetime")
   *
   */
  private $dateStart;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="date_end", type="datetime")
   */
  private $dateEnd;

  /**
   * @var string
   *
   * @ORM\Column(name="image", length=255, nullable=true)
   * @Image(id="special-offer/teaser")
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
   * @ORM\Column (name="published", type="boolean")
   */
  private $published = false;

  /**
   * @return int
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @param int $id
   */
  public function setId($id)
  {
    $this->id = $id;
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

  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }

  /**
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
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
   */
  public function setAnnounce($announce)
  {
    $this->announce = $announce;
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
   */
  public function setText($text)
  {
    $this->text = $text;
  }

  /**
   * @return \DateTime
   */
  public function getDateStart()
  {
    return $this->dateStart;
  }

  /**
   * @param \DateTime $dateStart
   */
  public function setDateStart($dateStart)
  {
    $this->dateStart = $dateStart;
  }

  /**
   * @return \DateTime
   */
  public function getDateEnd()
  {
    return $this->dateEnd;
  }

  /**
   * @param \DateTime $dateEnd
   */
  public function setDateEnd($dateEnd)
  {
    $this->dateEnd = $dateEnd;
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
   */
  public function setPublished($published)
  {
    $this->published = $published;
  }

  public function __toString()
  {
    return (string)$this->getTitle();
  }

  /**
   * Задает значения по умолчанию для сущности
   *
   * @ORM\PrePersist()
   *
   */
  public function setDefaultCreate()
  {
    if (!$this->dateStart)
    {
      $this->dateStart = new \DateTime();
      $this->dateEnd = strtotime("+1 month");
    }
  }

  public function getSlugSource()
  {
    return $this->getTitle();
  }

  /**
   * @return SpecialOfferImage
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

    return new SpecialOfferImage('teaser', $this->teaserImageFile, $this->getTeaserImageOptions());
  }

  /**
   * @param SpecialOfferImage $teaser
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
   * @return SpecialOffer
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
   * @return SpecialOffer
   */
  public function setTeaserImageOptions($teaserImageOptions)
  {
    $this->teaserImageOptions = $teaserImageOptions;
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
    
    return new UnprocessedImage('special-offer/teaser', $this->teaserImageFile, []);
  }
  
  public function setTeaserImageFileImage(ImageInterface $image = null)
  {
    $this->setTeaserImageFile($image ? $image->getResourceId() : null);
  }
  
  
}