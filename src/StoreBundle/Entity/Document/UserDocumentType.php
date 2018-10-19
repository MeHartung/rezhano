<?php

namespace StoreBundle\Entity\Document;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="StoreBundle\Repository\Document\UserDocumentTypeRepository")
 * @ORM\Table(name="user_document_type")
 */
class UserDocumentType extends Document
{
  /**
   * @var boolean
   * @ORM\Column(type="boolean", nullable=false, options={"default"=false})
   */
  protected $showIndividual=false;

  /**
   * @var boolean
   * @ORM\Column(type="boolean", nullable=false, options={"default"=false})
   */
  protected $showJuridical=false;

  /**
   * @var boolean
   * @ORM\Column(type="boolean", nullable=false, options={"default"=false})
   */
  protected $showEnterpreneur=false;

  /**
   * @var integer
   * @ORM\Column(type="integer", nullable=false, options={"default":0})
   */
  protected $positionIndividual=0;

  /**
   * @var integer
   * @ORM\Column(type="integer", nullable=false, options={"default":0})
   */
  protected $positionJuridical=0;

  /**
   * @var integer
   * @ORM\Column(type="integer", nullable=false, options={"default":0})
   */
  protected $positionEnterpreneur=0;

  /**
   * @return bool
   */
  public function isShowIndividual ()
  {
    return $this->showIndividual;
  }

  /**
   * @param bool $showIndividual
   * @return $this
   */
  public function setShowIndividual ($showIndividual)
  {
    $this->showIndividual = $showIndividual;
    return $this;
  }

  /**
   * @return bool
   */
  public function isShowJuridical (): bool
  {
    return $this->showJuridical;
  }

  /**
   * @param bool $showJuridical
   * @return $this
   */
  public function setShowJuridical ($showJuridical)
  {
    $this->showJuridical = $showJuridical;
    return $this;
  }

  /**
   * @return bool
   */
  public function isShowEnterpreneur ()
  {
    return $this->showEnterpreneur;
  }

  /**
   * @param bool $showEnterpreneur
   * @return $this
   */
  public function setShowEnterpreneur ($showEnterpreneur)
  {
    $this->showEnterpreneur = $showEnterpreneur;
    return $this;
  }

  /**
   * @return int
   */
  public function getPositionIndividual (): int
  {
    return $this->positionIndividual;
  }

  /**
   * @param int $positionIndividual
   * @return $this
   */
  public function setPositionIndividual ($positionIndividual)
  {
    $this->positionIndividual = $positionIndividual;
    return $this;
  }

  /**
   * @return int
   */
  public function getPositionJuridical ()
  {
    return $this->positionJuridical;
  }

  /**
   * @param int $positionJuridical
   * @return $this
   */
  public function setPositionJuridical ($positionJuridical)
  {
    $this->positionJuridical = $positionJuridical;
    return $this;
  }

  /**
   * @return int
   */
  public function getPositionEnterpreneur ()
  {
    return $this->positionEnterpreneur;
  }

  /**
   * @param int $positionEnterpreneur
   * @return $this
   */
  public function setPositionEnterpreneur ($positionEnterpreneur)
  {
    $this->positionEnterpreneur = $positionEnterpreneur;
    return $this;
  }
}