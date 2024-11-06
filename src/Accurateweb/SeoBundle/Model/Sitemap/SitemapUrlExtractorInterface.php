<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 26.09.2017
 * Time: 19:14
 */

namespace Accurateweb\SeoBundle\Model\Sitemap;


interface SitemapUrlExtractorInterface
{
  public function supports($model);

  public function extract($model);
}