<?php
/**
 * (c) 2017 ИП Рагозин Денис Николаевич. Все права защищены.
 *
 * Настоящий файл является частью программного продукта, разработанного ИП Рагозиным Денисом Николаевичем
 * (ОГРНИП 315668300000095, ИНН 660902635476).
 *
 * Алгоритм и исходные коды программного кода программного продукта являются коммерческой тайной
 * ИП Рагозина Денис Николаевича. Любое их использование без согласия ИП Рагозина Денис Николаевича рассматривается,
 * как нарушение его авторских прав.
 *
 * Ответственность за нарушение авторских прав наступает в соответствии с действующим законодательством РФ.
 */

/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 28.09.2017
 * Time: 11:30
 */

namespace Excam\Sitemap\Loader;

use Accurateweb\SeoBundle\Model\Sitemap\SitemapLoaderInterface;
use Doctrine\ORM\EntityManager;

class SpecialOfferLoader implements SitemapLoaderInterface
{
  private $entityManager;

  public function __construct(EntityManager $entityManager)
  {
    $this->entityManager = $entityManager;
  }

  public function load()
  {
    return $this->entityManager
      ->getRepository('StoreBundle:Text\SpecialOffer')
      ->createQueryBuilder('a')
      ->where('a.published > 0')
      ->getQuery()
      ->getResult();
  }
}