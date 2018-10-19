<?php

namespace StoreBundle\Entity\Document;

use Doctrine\ORM\Mapping as ORM;
use StoreBundle\Entity\User\User;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

/**
 * Документы, которые загружает пользователь при регистрации
 * @ORM\Entity()
 * @DoctrineAssert\UniqueEntity(fields={"user", "documentType"})
 * @ORM\Table(name="user_document", uniqueConstraints={
 *        @ORM\UniqueConstraint(name="user_type",
 *            columns={"user_id", "document_type_id"})
 *    })
 */
class UserDocument extends Document
{
  /**
   * @var User
   * @ORM\ManyToOne(targetEntity="StoreBundle\Entity\User\User", inversedBy="documents")
   * @ORM\JoinColumn(name="user_id", nullable=true)
   */
  private $user;

  /**
   * @var UserDocumentType
   * @ORM\ManyToOne(targetEntity="StoreBundle\Entity\Document\UserDocumentType")
   * @ORM\JoinColumn(name="document_type_id", nullable=false)
   */
  private $documentType;

  /**
   * @var string
   * @ORM\Column(nullable=true)
   */
  private $uuid;

  /**
   * @return User
   */
  public function getUser ()
  {
    return $this->user;
  }

  /**
   * @param User $user
   * @return $this
   */
  public function setUser (User $user)
  {
    $this->user = $user;
    return $this;
  }

  /**
   * @return UserDocumentType
   */
  public function getDocumentType ()
  {
    return $this->documentType;
  }

  /**
   * @param UserDocumentType $documentType
   * @return $this
   */
  public function setDocumentType (UserDocumentType $documentType)
  {
    $this->documentType = $documentType;
    return $this;
  }

  /**
   * @return string
   */
  public function getUuid ()
  {
    return $this->uuid;
  }

  /**
   * @param string $uuid
   * @return $this
   */
  public function setUuid (string $uuid)
  {
    $this->uuid = $uuid;
    return $this;
  }
}