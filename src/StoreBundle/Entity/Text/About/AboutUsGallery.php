<?php


namespace StoreBundle\Entity\Text\About;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="StoreBundle\Repository\Text\About\AboutUsGalleryRepository")
 * @ORM\Table(name="about_us_gallery")
 */
class AboutUsGallery
{
  /**
   * @var int
   * @ORM\Column(type="integer")
   * @ORM\Id()
   * @ORM\GeneratedValue()
   */
  private $id;
  
  /**
   * @var string
   * @ORM\Column(type="string", length=128)
   */
  private $title;
  
  /**
   * @var ArrayCollection
   *
   * @ORM\OneToMany(targetEntity="StoreBundle\Entity\Text\About\AboutUsImage", mappedBy="gallery", cascade={"remove", "persist"})
   * @ORM\OrderBy({"position" = "ASC"})
   */
  private $images;
  
  public function __construct()
  {
    $this->images = new ArrayCollection();
  }
  
  /**
   * @return int
   */
  public function getId(): ?int
  {
    return $this->id;
  }
  
  /**
   * @param int $id
   */
  public function setId(?int $id): void
  {
    $this->id = $id;
  }
  
  /**
   * @return ArrayCollection
   */
  public function getImages()
  {
    return $this->images;
  }
  
  /**
   * @param ArrayCollection $images
   */
  public function setImages(?ArrayCollection $images): void
  {
    $this->images = $images;
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
  public function setTitle(?string $title): void
  {
    $this->title = $title;
  }
 
  public function __toString()
  {
    return $this->getTitle() ? $this->getTitle() : '';
  }
  
}