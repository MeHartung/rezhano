<?php

namespace StoreBundle\Entity\Document;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Document
 *
 * @ORM\Table(name="document")
 * @ORM\Entity(repositoryClass="StoreBundle\Repository\Document\DocumentRepository")
 * @ORM\InheritanceType(value="JOINED")
 * @ORM\DiscriminatorColumn(name="type")
 * @ORM\DiscriminatorMap(value={"registration"="RegistrationDocument", "user"="UserDocument", "document_type"="UserDocumentType"})
 */
abstract class Document
{
  /**
   * @var int
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id;

  /**
   * @var string
   *
   * @ORM\Column(name="name", type="string", length=255)
   */
  protected $name;

  /**
   * @var string
   *
   * @ORM\Column(name="file", type="string", length=255)
   * @Assert\NotNull()
   */
  protected $file;

  /**
   * @var \Datetime|null
   *
   * @ORM\Column(name="createdAt", type="datetime", nullable=true)
   * @Gedmo\Timestampable(on="create")
   */
  protected $createdAt;

  /**
   * @var \DateTime|null
   *
   * @ORM\Column(name="updatedAt", type="datetime", nullable=true)
   * @Gedmo\Timestampable(on="update")
   */
  protected $updatedAt;

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
   * Set name.
   *
   * @param string $name
   *
   * @return Document
   */
  public function setName ($name)
  {
    $this->name = $name;

    return $this;
  }

  /**
   * Get name.
   *
   * @return string
   */
  public function getName ()
  {
    return $this->name;
  }

  /**
   * Set file.
   *
   * @param string $file
   *
   * @return Document
   */
  public function setFile ($file)
  {
    if ($file)
    {
      $this->file = $file;
    }

    return $this;
  }

  /**
   * Get file.
   *
   * @return string
   */
  public function getFile ()
  {
    return $this->file;
  }

  /**
   * Set createdAt.
   *
   * @param \Datetime|null $createdAt
   *
   * @return Document
   */
  public function setCreatedAt ($createdAt = null)
  {
    $this->createdAt = $createdAt;

    return $this;
  }

  /**
   * Get createdAt.
   *
   * @return \Datetime|null
   */
  public function getCreatedAt ()
  {
    return $this->createdAt;
  }

  /**
   * Set updatedAt.
   *
   * @param \DateTime|null $updatedAt
   *
   * @return Document
   */
  public function setUpdatedAt ($updatedAt = null)
  {
    $this->updatedAt = $updatedAt;

    return $this;
  }

  /**
   * Get updatedAt.
   *
   * @return \DateTime|null
   */
  public function getUpdatedAt ()
  {
    return $this->updatedAt;
  }

  public function __toString ()
  {
    if ($this->getName())
    {
      return $this->getName();
    }

    return 'Новый документ';
  }
}
