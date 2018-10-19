<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 27.09.2017
 * Time: 19:13
 */

namespace Excam\Sitemap\Loader;

use Accurateweb\SeoBundle\Model\Sitemap\SitemapLoaderInterface;
use StoreBundle\Repository\Store\Catalog\Product\ProductRepository;

class ProductLoader implements SitemapLoaderInterface
{
  private $repository;

  public function __construct(ProductRepository $productRepository)
  {
    $this->repository = $productRepository;
  }

  public function load()
  {
    return $this->repository
                ->createQueryBuilder('p')
                ->where('p.published > 0')
                ->getQuery()
                ->getResult();
  }

}