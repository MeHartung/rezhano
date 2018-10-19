<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 26.09.2017
 * Time: 18:40
 */

namespace Excam\Sitemap;

interface SitemapBuilderInterface
{
  public function build(Sitemap $sitemap, array $options = array());
}