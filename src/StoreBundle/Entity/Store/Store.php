<?php

namespace StoreBundle\Entity\Store;

use Accurateweb\ImagingBundle\Filter\CropFilterOptionsResolver;
use Accurateweb\ImagingBundle\Filter\FilterChain;
use Accurateweb\MediaBundle\Model\Image\ImageAwareInterface;
use Accurateweb\MediaBundle\Model\Media\ImageInterface;
use Accurateweb\MediaBundle\Model\Thumbnail\ImageThumbnail;
use Accurateweb\MediaBundle\Model\Thumbnail\ThumbnailDefinition;
use Doctrine\ORM\Mapping as ORM;
use Accurateweb\MediaBundle\Annotation\Image;
use Accurateweb\MediaBundle\Annotation\Thumbnail;
use Accurateweb\MediaBundle\Annotation\Filter;
use StoreBundle\Media\Text\UnprocessedImage;

/**
 * Контакты магазина
 *
 * @ORM\Table(name="store")
 * @ORM\Entity(repositoryClass="StoreBundle\Repository\Store\StoreRepository")
 */
class Store implements ImageInterface, ImageAwareInterface
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
   * @ORM\Column(type="string", length=255, nullable=false)
   */
  private $name;

  /**
   * @var string|null
   * @ORM\Column(type="string", length=255, nullable=true)
   */
  private $address;

  /**
   * @var string|null
   * @ORM\Column(type="string", length=255, nullable=true)
   */
  private $phone;

  /**
   * @var string|null
   * @ORM\Column(type="string", length=255, nullable=true)
   */
  private $email;

  /**
   * @var bool
   * @ORM\Column(type="boolean", nullable=false, options={"default"=0})
   */
  private $showFooter = false;

  /**
   * @var float
   * @ORM\Column(type="decimal", precision=9, scale=6, nullable=true)
   */
  protected $latitude;

  /**
   * @var float
   * @ORM\Column(type="decimal", precision=9, scale=6, nullable=true)
   */
  protected $longitude;

  /**
   * @var string|null
   * @ORM\Column(type="string", length=255, nullable=true)
   * @Image(id="store",thumbnails={
   *   @Thumbnail(id="contact",filters={
   *    @Filter(id="resize", options={"size"="211x211"}
   *   )})
   * })
   */
  protected $teaser;

  /**
   * @var string|null
   * @ORM\Column(type="string", length=255, nullable=true)
   */
  protected $workTime;

  /**
   * @var string|null
   * @ORM\Column(type="text", nullable=true)
   */
  protected $description;

  /**
   * @var string|null
   * @ORM\Column(type="text",nullable=true)
   */
  protected $fullAddress;

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
  public function getName ()
  {
    return $this->name;
  }

  /**
   * @param string $name
   * @return $this
   */
  public function setName ($name)
  {
    $this->name = $name;
    return $this;
  }

  /**
   * @return null|string
   */
  public function getAddress ()
  {
    return $this->address;
  }

  /**
   * @param null|string $address
   * @return $this
   */
  public function setAddress ($address)
  {
    $this->address = $address;
    return $this;
  }

  /**
   * @return null|string
   */
  public function getPhone ()
  {
    return $this->phone;
  }

  /**
   * @param null|string $phone
   * @return $this
   */
  public function setPhone ($phone)
  {
    $this->phone = $phone;
    return $this;
  }

  /**
   * @return null|string
   */
  public function getEmail ()
  {
    return $this->email;
  }

  /**
   * @param null|string $email
   * @return $this
   */
  public function setEmail ($email)
  {
    $this->email = $email;
    return $this;
  }

  /**
   * @return bool
   */
  public function isShowFooter ()
  {
    return $this->showFooter;
  }

  /**
   * @param bool $showFooter
   * @return $this
   */
  public function setShowFooter ($showFooter)
  {
    $this->showFooter = $showFooter;
    return $this;
  }

  /**
   * @return float
   */
  public function getLatitude ()
  {
    return $this->latitude;
  }

  /**
   * @param float $latitude
   * @return $this
   */
  public function setLatitude ($latitude)
  {
    $this->latitude = $latitude;
    return $this;
  }

  /**
   * @return float
   */
  public function getLongitude ()
  {
    return $this->longitude;
  }

  /**
   * @param float $longitude
   * @return $this
   */
  public function setLongitude ($longitude)
  {
    $this->longitude = $longitude;
    return $this;
  }

  /**
   * @return null|string
   */
  public function getTeaser ()
  {
    return $this->teaser;
  }

  /**
   * @param null|string $teaser
   * @return $this
   */
  public function setTeaser ($teaser)
  {
    if (!is_null($teaser))
    {
      $this->teaser = $teaser;
    }

    return $this;
  }

  /**
   * @return null|string
   */
  public function getWorkTime ()
  {
    return $this->workTime;
  }

  /**
   * @param null|string $workTime
   * @return $this
   */
  public function setWorkTime ($workTime)
  {
    $this->workTime = $workTime;
    return $this;
  }

  /**
   * @return null|string
   */
  public function getDescription ()
  {
    return $this->description;
  }

  /**
   * @param null|string $description
   * @return $this
   */
  public function setDescription ($description)
  {
    $this->description = $description;
    return $this;
  }

  /**
   * Get thumbnail definitions
   *
   * @return ThumbnailDefinition[]
   */
  public function getThumbnailDefinitions ()
  {
    return [
      new ThumbnailDefinition('contact', new FilterChain([
        [
          'id' => 'crop',
          'options' => [],
          'resolver' => new CropFilterOptionsResolver()],
        ['id' => 'resize', 'options' => ['size' => '211x211']]
      ])),
    ];
  }

  /**
   * Get thumbnail
   *
   * @param $id string Thumbnail id (as described in definition)
   * @return ImageThumbnail
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

  public function getResourceId ()
  {
    return $this->teaser;
  }

  public function setResourceId ($id)
  {
    $this->teaser = $id;
  }

  public function getTeaserImage()
  {
    if (null == $this->teaser)
    {
      return null;
    }

    return new UnprocessedImage('contact/'.$this->id.'teaser', $this->teaser, []);
  }

  public function setTeaserImage(ImageInterface $image = null)
  {
    $this->setResourceId($image?$image->getResourceId():null);
    return $this;
  }

  public function __toString ()
  {
    return $this->getName()?:'Новый магазин';
  }

  public function getImage ($id = null)
  {
    return $this;
  }

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
   * @return null|string
   */
  public function getFullAddress ()
  {
    return $this->fullAddress;
  }

  /**
   * @param null|string $fullAddress
   * @return $this
   */
  public function setFullAddress ($fullAddress)
  {
    $this->fullAddress = $fullAddress;
    return $this;
  }




}