<?php

namespace StoreBundle\Entity\Store\Excursion;

use Accurateweb\ImagingBundle\Filter\CropFilterOptionsResolver;
use Accurateweb\ImagingBundle\Filter\FilterChain;
use Accurateweb\MediaBundle\Annotation\Image;
use Accurateweb\MediaBundle\Model\Image\ImageAwareInterface;
use Accurateweb\MediaBundle\Model\Media\ImageInterface;
use Accurateweb\MediaBundle\Model\Thumbnail\ImageThumbnail;
use Accurateweb\MediaBundle\Model\Thumbnail\ThumbnailDefinition;
use Doctrine\ORM\Mapping as ORM;
use StoreBundle\Media\Text\UnprocessedImage;

/**
 * ExcursionBanner
 *
 * @ORM\Table(name="excursion_banner")
 * @ORM\Entity()
 */
class ExcursionBanner implements ImageAwareInterface
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
   * @Image(id="excursion/teaser")
   */
  private $teaser;

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


  public function getTeaserImage()
  {
    if (null === $this->teaser)
    {
      return null;
    }

    return new UnprocessedImage('excursion/teaser', $this->teaser, []);
  }

  public function setTeaserImage(ImageInterface $image)
  {
    $this->setTeaser($image ? $image->getResourceId() : null);
  }

  /**
   * Set enabled.
   *
   * @param bool $enabled
   *
   * @return $this
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
      case 'excursion/teaser':
        return $this->getTeaserImage();
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
      case 'excursion/teaser':
        $this->setTeaserImage($image);
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
      new ThumbnailDefinition('contact', new FilterChain([
        [
          'id' => 'crop',
          'options' => [],
          'resolver' => new CropFilterOptionsResolver()],
        ['id' => 'resize', 'options' => ['size' => '490x723']]
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
    return $this->getId()?sprintf('%s', $this->getId()):'Новый';
  }
}
