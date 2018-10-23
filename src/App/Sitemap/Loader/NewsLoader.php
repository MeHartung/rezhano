<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 28.09.2017
 * Time: 11:30
 */

namespace App\Sitemap\Loader;

use Accurateweb\SeoBundle\Model\Sitemap\SitemapLoaderInterface;
use Doctrine\ORM\EntityManager;

class NewsLoader implements SitemapLoaderInterface
{
  private $entityManager;

  public function __construct(EntityManager $entityManager)
  {
    $this->entityManager = $entityManager;
  }

  public function load()
  {
    return $this->entityManager
      ->getRepository('StoreBundle:Text\News')
      ->createQueryBuilder('a')
      ->where('a.published > 0')
      ->getQuery()
      ->getResult();
  }
}