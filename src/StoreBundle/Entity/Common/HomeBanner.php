<?php

namespace StoreBundle\Entity\Common;

use Accurateweb\ImagingBundle\Filter\CropFilterOptionsResolver;
use Accurateweb\ImagingBundle\Filter\FilterChain;
use Accurateweb\MediaBundle\Annotation\Image;
use Accurateweb\MediaBundle\Model\Image\ImageAwareInterface;
use Accurateweb\MediaBundle\Model\Media\ImageInterface;
use Accurateweb\MediaBundle\Model\Media\Thumbnail;
use Accurateweb\MediaBundle\Model\Thumbnail\ImageThumbnail;
use Accurateweb\MediaBundle\Model\Thumbnail\ThumbnailDefinition;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use StoreBundle\Media\Text\UnprocessedImage;

/**
 * HomeBanner
 *
 * @ORM\Table(name="common_home_banner")
 * @ORM\Entity(repositoryClass="StoreBundle\Repository\Common\HomeBannerRepository")
 */
class HomeBanner implements ImageAwareInterface
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
   * @ORM\Column(name="teaser", type="string", length=255)
   * @Image(id="homepage-banner/teaser")
   */
  private $teaser;

  /**
   * @var string
   *
   * @ORM\Column()
   * @Image(id="homepage-banner/text")
   */
  private $textImageFile;

  /**
   * @var ?string
   *
   * @ORM\Column(length=255, nullable=true)
   */
  private $text;

  /**
   * @var ?string
   *
   * @ORM\Column(length=16, nullable=true)
   */
  private $buttonLabel;

  /**
   * @var string
   * @ORM\Column(name="url", type="string", length=1000, nullable=true)
   */
  private $url;

  /**
   * @var int
   * @Gedmo\SortablePosition()
   * @ORM\Column(name="position", type="integer", nullable=true)
   */
  private $position;

  /**
   * @var bool
   *
   * @ORM\Column(name="enabled", type="boolean")
   */
  private $enabled = false;


  /**
   * Get id.
   *
   * @return int
   */
  public function getId ()
  {
    return $this->id;
  }

  /**
   * @return string
   */
  public function getTeaser ()
  {
    return $this->teaser;
  }

  /**
   * @param string $teaser
   * @return $this
   */
  public function setTeaser($teaser)
  {
    if (null !== $teaser)
    {
      $this->teaser = $teaser;
    }

    return $this;
  }

  /**
   * @return string|null
   */
  public function getTextImageFile()
  {
    return $this->textImageFile;
  }

  /**
   * @param string $textImageFile
   * @return HomeBanner
   */
  public function setTextImageFile($textImageFile): HomeBanner
  {
    if (null !== $textImageFile)
    {
      $this->textImageFile = $textImageFile;
    }
    return $this;
  }



  public function getTeaserImage()
  {
    if (null === $this->teaser)
    {
      return null;
    }

    return new UnprocessedImage('homepage-banner/teaser', $this->teaser, []);
  }

  public function setTeaserImage(ImageInterface $image)
  {
    $this->setTeaser($image ? $image->getResourceId() : null);
  }

  public function getTextImageFileImage()
  {
    if (null === $this->textImageFile)
    {
      return null;
    }

    return new UnprocessedImage('homepage-banner/text', $this->textImageFile, []);
  }

  public function setTextImageFileImage(ImageInterface $image)
  {
    $this->setTextImageFile($image ? $image->getResourceId() : null);

    return $this;
  }

  /**
   * @return string
   */
  public function getUrl ()
  {
    return $this->url;
  }

  /**
   * @param string $url
   * @return $this
   */
  public function setUrl (string $url)
  {
    $this->url = $url;
    return $this;
  }

  /**
   * Set position.
   *
   * @param int $position
   *
   * @return HomeBanner
   */
  public function setPosition ($position)
  {
    $this->position = $position;

    return $this;
  }

  /**
   * Get position.
   *
   * @return int
   */
  public function getPosition ()
  {
    return $this->position;
  }

  /**
   * Set enabled.
   *
   * @param bool $enabled
   *
   * @return HomeBanner
   */
  public function setEnabled ($enabled)
  {
    $this->enabled = $enabled;

    return $this;
  }

  /**
   * Get enabled.
   *
   * @return bool
   */
  public function isEnabled ()
  {
    return $this->enabled;
  }

  /**
   * @param null $id
   * @return ImageInterface
   */
  public function getImage ($id = null)
  {
    switch ($id)
    {
      case null:
      case 'homepage-banner/teaser':
        return $this->getTeaserImage();
      case 'homepage-banner/text':
        return $this->getTextImageFileImage();
    }

    throw new \InvalidArgumentException();
  }

  /**
   * @param ImageInterface $image
   * @return $this
   */
  public function setImage (ImageInterface $image)
  {
    if (!$image)
    {
      return $this;
    }

    switch ($image->getId())
    {
      case null:
      case 'homepage-banner/teaser':
        $this->setTeaserImage($image);
        break;
      case 'homepage-banner/text':
        $this->setTextImageFileImage($image);
        break;
      default:
        throw new \InvalidArgumentException();
    }

    return $this;
  }

  public function getImageOptions ($id)
  {
    return null;
  }

  public function setImageOptions ($id)
  {
    return $this;
  }

  /**
   * @return array
   */
  public function getThumbnailDefinitions ()
  {
    return [
      new ThumbnailDefinition('banner', new FilterChain([
        [
          'id' => 'crop',
          'options' => [],
          'resolver' => new CropFilterOptionsResolver()],
        ['id' => 'resize', 'options' => ['size' => '617x301']]
      ])),
    ];
  }

  /**
   * @param string $id
   * @return ImageThumbnail
   * @throws \Exception
   */
  public function getThumbnail ($id)
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

  public function __toString ()
  {
    return $this->getId()?sprintf('Баннер #%s', $this->getId()):'Новый баннер';
  }

  /**
   * @return mixed
   */
  public function getText()
  {
    return $this->text;
  }

  /**
   * @param mixed $text
   * @return HomeBanner
   */
  public function setText($text)
  {
    $this->text = $text;
    return $this;
  }

  /**
   * @return mixed
   */
  public function getButtonLabel()
  {
    return $this->buttonLabel;
  }

  /**
   * @param mixed $buttonLabel
   * @return HomeBanner
   */
  public function setButtonLabel($buttonLabel)
  {
    $this->buttonLabel = $buttonLabel;
    return $this;
  }


}
