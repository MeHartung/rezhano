<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 27.09.2017
 * Time: 19:37
 */

namespace App\Sitemap\Loader;

use Accurateweb\SeoBundle\Model\Sitemap\SitemapLoaderInterface;
use Doctrine\ORM\EntityManager;

class ArticleLoader implements SitemapLoaderInterface
{
  private $entityManager;

  public function __construct(EntityManager $entityManager)
  {
    $this->entityManager = $entityManager;
  }

  public function load()
  {
    return $this->entityManager
             ->getRepository('StoreBundle:Text\Article')
             ->createQueryBuilder('a')
             ->where('a.published > 0')
             ->getQuery()
             ->getResult();
  }
}