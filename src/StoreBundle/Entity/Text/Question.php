<?php

namespace StoreBundle\Entity\Text;


use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Question
 * @ORM\Table(name="questions")
 * @ORM\Entity()
 */
class Question
{
  /**
   * @var int|null
   * @ORM\Column(type="integer")
   * @ORM\Id()
   * @ORM\GeneratedValue()
   */
  private $id;
  
  /**
   * @var string|null
   * @ORM\Column(length=255)
   * @Assert\NotBlank()
   */
  private $fio;
  
  /**
   * @var string|null
   * @ORM\Column(nullable=true)
   * @ Assert\Email()
   */
  private $email;
  
  /**
   * @var string|null
   * @ORM\Column(nullable=true)
   */
  private $phone;
  
  /**
   * @var string|null
   *
   * @ORM\Column(type="text")
   * @Assert\NotBlank()
   */
  private $text;
  
  /**
   * @var \DateTime|null
   * @ORM\Column(type="datetime")
   * @Gedmo\Timestampable()
   */
  private $createdAt;
  
  /**
   * @var \DateTime|null
   * @ORM\Column(type="datetime", nullable=true)
   */
  private $answerAt;
  
  /**
   * @var string|null
   *
   * @ORM\Column(type="text", nullable=true)
   */
  private $answer;
  
  /**
   * @return int|null
   */
  public function getId(): ?int
  {
    return $this->id;
  }
  
  /**
   * @param int|null $id
   */
  public function setId(?int $id): void
  {
    $this->id = $id;
  }
  
  /**
   * @return null|string
   */
  public function getFio(): ?string
  {
    return $this->fio;
  }
  
  /**
   * @param null|string $fio
   */
  public function setFio(?string $fio): void
  {
    $this->fio = $fio;
  }
  
  /**
   * @return null|string
   */
  public function getEmail(): ?string
  {
    return $this->email;
  }
  
  /**
   * @param null|string $email
   */
  public function setEmail(?string $email): void
  {
    $this->email = $email;
  }
  
  /**
   * @return null|string
   */
  public function getPhone(): ?string
  {
    return $this->phone;
  }
  
  /**
   * @param null|string $phone
   */
  public function setPhone(?string $phone): void
  {
    $this->phone = $phone;
  }
  
  /**
   * @return null|string
   */
  public function getText(): ?string
  {
    return $this->text;
  }
  
  /**
   * @param null|string $text
   */
  public function setText(?string $text): void
  {
    $this->text = $text;
  }
  
  /**
   * @return \DateTime|null
   */
  public function getCreatedAt(): ?\DateTime
  {
    return $this->createdAt;
  }
  
  /**
   * @param \DateTime|null $createdAt
   */
  public function setCreatedAt(?\DateTime $createdAt): void
  {
    $this->createdAt = $createdAt;
  }
  
  /**
   * @return \DateTime|null
   */
  public function getAnswerAt(): ?\DateTime
  {
    return $this->answerAt;
  }
  
  /**
   * @param \DateTime|null $answerAt
   */
  public function setAnswerAt(?\DateTime $answerAt): void
  {
    $this->answerAt = $answerAt;
  }
  
  /**
   * @return null|string
   */
  public function getAnswer(): ?string
  {
    return $this->answer;
  }
  
  /**
   * @param null|string $answer
   */
  public function setAnswer(?string $answer): void
  {
    $this->answer = $answer;
  }
  
  public function __toString()
  {
    return $this->getId() ? 'Вопрос от ' . $this->getFio() : '';
  }
}