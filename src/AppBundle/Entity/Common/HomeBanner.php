<?php

namespace AppBundle\Entity\Common;

use Accurateweb\ImagingBundle\Filter\CropFilterOptionsResolver;
use Accurateweb\ImagingBundle\Filter\FilterChain;
use Accurateweb\MediaBundle\Model\Image\ImageAwareInterface;
use Accurateweb\MediaBundle\Model\Media\ImageInterface;
use Accurateweb\MediaBundle\Model\Media\Thumbnail;
use Accurateweb\MediaBundle\Model\Thumbnail\ImageThumbnail;
use Accurateweb\MediaBundle\Model\Thumbnail\ThumbnailDefinition;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * HomeBanner
 *
 * @ORM\Table(name="common_home_banner")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Common\HomeBannerRepository")
 */
class HomeBanner implements ImageAwareInterface, ImageInterface
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
   */
  private $teaser;

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
  public function setTeaser ($teaser)
  {
    if (null !== $teaser)
    {
      $this->teaser = $teaser;
    }

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
    return $this;
  }

  /**
   * @param ImageInterface $image
   * @return $this
   */
  public function setImage (ImageInterface $image)
  {
    $this->setResourceId($image->getResourceId());

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

  /**
   * @return string
   */
  public function getResourceId ()
  {
    return $this->getTeaser();
  }

  /**
   * @param $id
   */
  public function setResourceId ($id)
  {
    $this->setTeaser($id);
  }

  public function __toString ()
  {
    return $this->getId()?sprintf('Баннер #%s', $this->getId()):'Новый баннер';
  }
}
