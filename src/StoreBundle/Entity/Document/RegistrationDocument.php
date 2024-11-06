<?php

namespace StoreBundle\Entity\Document;

use Doctrine\ORM\Mapping as ORM;

/**
 * Документы на странице регистрации
 * @ORM\Entity()
 * @ORM\Table(name="registration_document")
 */
class RegistrationDocument extends Document
{
  /**
   * @var boolean
   * @ORM\Column(name="`show`",type="boolean", nullable=false, options={"default"=false})
   */
  protected $show=false;

  /**
   * @return bool
   */
  public function isShow ()
  {
    return $this->show;
  }

  /**
   * @param bool $show
   * @return $this
   */
  public function setShow ($show)
  {
    $this->show = $show;
    return $this;
  }
}