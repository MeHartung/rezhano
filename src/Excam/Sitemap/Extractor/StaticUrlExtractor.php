<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 28.09.2017
 * Time: 11:31
 */

namespace Excam\Sitemap\Extractor;


use Accurateweb\SeoBundle\Model\Sitemap\SitemapUrl;
use Accurateweb\SeoBundle\Model\Sitemap\SitemapUrlExtractorInterface;

class StaticUrlExtractor implements SitemapUrlExtractorInterface
{
  public function supports($model)
  {
    return $model instanceof SitemapUrl;
  }

  public function extract($model)
  {
    return $model;
  }
}