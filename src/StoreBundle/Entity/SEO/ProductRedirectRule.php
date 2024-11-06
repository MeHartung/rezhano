<?php


namespace StoreBundle\Entity\SEO;

use Doctrine\ORM\Mapping as ORM;


/**
 * Class ProductRedirectRules
 * Таблица для сопоставления слагов товаров.
 * Слаги не связаны с товарами, чтобы товары можно было удалить
 *
 * @package StoreBundle\Entity\SEO
 * @ORM\Entity()
 * @ORM\Table(name="product_redirect_rules")
 */
class ProductRedirectRule
{
  
  /**
   * @var int
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   * @ORM\Column(type="integer")
   */
  private $id;
  
  /**
   * @var string|null
   * @ORM\Column(type="string", length=512)
   */
  private $slugFrom;
  
  /**
   * @var string|null
   * @ORM\Column(type="string", length=512)
   */
  private $slugTo;
  
  /**
   * @return int
   */
  public function getId(): int
  {
    return $this->id;
  }
  
  /**
   * @param int $id
   */
  public function setId(int $id): void
  {
    $this->id = $id;
  }
  
  /**
   * @return null|string
   */
  public function getSlugFrom(): ?string
  {
    return $this->slugFrom;
  }
  
  /**
   * @param null|string $slugFrom
   */
  public function setSlugFrom(?string $slugFrom): void
  {
    $this->slugFrom = $slugFrom;
  }
  
  /**
   * @return null|string
   */
  public function getSlugTo(): ?string
  {
    return $this->slugTo;
  }
  
  /**
   * @param null|string $slugTo
   */
  public function setSlugTo(?string $slugTo): void
  {
    $this->slugTo = $slugTo;
  }
  
}