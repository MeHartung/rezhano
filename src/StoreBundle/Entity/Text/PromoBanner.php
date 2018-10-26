<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Entity\Text;

use Accurateweb\MediaBundle\Annotation\Image;
use Accurateweb\MediaBundle\Model\Image\ImageAwareInterface;
use Accurateweb\MediaBundle\Model\Media\ImageInterface;
use Doctrine\ORM\Mapping as ORM;
use StoreBundle\Media\Text\UnprocessedImage;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Промо-блоки на главной странице
 *
 * @package StoreBundle\Entity\Text
 *
 * @ORM\Entity()
 * @ORM\Table()
 */
class PromoBanner implements ImageAwareInterface
{
  /**
   * @var integer
   *
   * @ORM\Column(type="integer")
   * @ORM\Id()
   * @ORM\GeneratedValue()
   */
  private $id;

  /**
   * Тизерная большая картинка
   *
   * @var string
   *
   * @ORM\Column(length=255)
   * @Image(id="homepage-promo-banner/teaser")
   */
  private $teaserImageFile;

  /**
   * @var string
   *
   * @ORM\Column()
   * @Image(id="homepage-promo-banner/text")
   */
  private $textImageFile;

  /**
   * Тизерный текст
   *
   * @var string
   *
   * @ORM\Column(length=1024)
   */
  private $text;

  /**
   * Адрес ссылки
   *
   * @var string
   *
   * @ORM\Column(length=255)
   */
  private $url;

  /**
   * Текст в кнопке
   *
   * @var string
   *
   * @ORM\Column(length=32)
   */
  private $buttonText;

  /**
   * @var boolean
   *
   * @ORM\Column(type="boolean")
   */
  private $published;

  /**
   * @var integer|null
   * @ORM\Column(type="integer")
   * @Gedmo\SortablePosition()
   */
  private $position;

  /**
   * @return int
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @param int $id
   * @return PromoBanner
   */
  public function setId($id)
  {
    $this->id = $id;
    return $this;
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
   * @return PromoBanner
   */
  public function setTeaserImageFile($teaserImageFile)
  {
    if (null !== $teaserImageFile)
    {
      $this->teaserImageFile = $teaserImageFile;
    }

    return $this;
  }

  /**
   * @return string
   */
  public function getTextImageFile()
  {
    return $this->textImageFile;
  }

  /**
   * @param string $textImageFile
   * @return PromoBanner
   */
  public function setTextImageFile($textImageFile)
  {
    if (null !== $textImageFile)
    {
      $this->textImageFile = $textImageFile;
    }

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
   * @return PromoBanner
   */
  public function setText($text)
  {
    $this->text = $text;
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
   * @return PromoBanner
   */
  public function setUrl($url)
  {
    $this->url = $url;
    return $this;
  }

  /**
   * @return string
   */
  public function getButtonText()
  {
    return $this->buttonText;
  }

  /**
   * @param string $buttonText
   * @return PromoBanner
   */
  public function setButtonText($buttonText)
  {
    $this->buttonText = $buttonText;
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
   * @return PromoBanner
   */
  public function setPublished($published)
  {
    $this->published = $published;
    return $this;
  }

  /**
   * @return int|null
   */
  public function getPosition()
  {
    return $this->position;
  }

  /**
   * @param int|null $position
   * @return PromoBanner
   */
  public function setPosition($position)
  {
    $this->position = $position;
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

    return new UnprocessedImage('homepage-promo-banner/'.$this->id.'teaser', $this->teaserImageFile, []);
  }

  public function setTeaserImageFileImage(ImageInterface $image = null)
  {
    $this->setTeaserImageFile($image ? $image->getResourceId() : null);
  }

  public function getTextImageFileImage()
  {
    if (null == $this->textImageFile)
    {
      return null;
    }

    return new UnprocessedImage('homepage-promo-banner/'.$this->id.'text', $this->textImageFile, []);
  }

  public function setTextImageFileImage(ImageInterface $image = null)
  {
      $this->setTextImageFile($image ? $image->getResourceId() : null);
  }

  /**
   * @param null $id
   *
   * @return ImageInterface
   */
  public function getImage($id = null)
  {
    switch ($id)
    {
      case 'homepage-promo-banner/teaser':
        return $this->getTeaserImageFileImage();
      case 'homepage-promo-banner/text':
        return $this->getTextImageFileImage();
    }

    throw new \InvalidArgumentException(sprintf('Unknown image id "%s"', $id));
  }

  public function setImage(ImageInterface $image)
  {
    if (null === $image)
    {
      //@TODO: Что делать, если у нас две картинки?
      return;
    }

    switch ($image->getId())
    {
      case 'homepage-promo-banner/teaser':
        $this->setTeaserImageFile($image->getResourceId());
        break;
      case 'homepage-promo-banner/text':
        $this->setTextImageFile($image->getResourceId());
        break;
      default:
        throw new \InvalidArgumentException();
    }
  }

  public function getImageOptions($id)
  {
    return [];
  }

  public function setImageOptions($id)
  {
    //Нет опций - нет проблем.
  }

  public function __toString()
  {
    return $this->getId() ? mb_substr($this->getText(), 0, 16) : 'Новый промо-баннер';
  }

}