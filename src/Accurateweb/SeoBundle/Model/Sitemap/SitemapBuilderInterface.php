<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 26.09.2017
 * Time: 18:40
 */

namespace App\Sitemap;

interface SitemapBuilderInterface
{
  public function build(Sitemap $sitemap, array $options = array());
}