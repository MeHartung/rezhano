<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 26.09.2017
 * Time: 19:29
 */

namespace App\Sitemap\Loader;

use Accurateweb\SeoBundle\Model\Sitemap\SitemapLoaderInterface;
use StoreBundle\Repository\Store\Catalog\Taxonomy\TaxonRepository;

class StaticTaxonLoader implements SitemapLoaderInterface
{
  private $taxonRepository;

  public function __construct(TaxonRepository $taxonRepository)
  {
    $this->taxonRepository = $taxonRepository;
  }

  public function load()
  {
    return $this->taxonRepository
                ->createQueryBuilder('t')
                ->where('t.treeLeft > 1')
                ->getQuery()
                ->getResult();
  }
}