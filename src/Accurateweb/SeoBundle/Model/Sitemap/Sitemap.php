<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 26.09.2017
 * Time: 18:31
 */

namespace Accurateweb\SeoBundle\Model\Sitemap;

class Sitemap
{
  private $urls = array();

  /**
   * @param SitemapUrl $url
   */
  public function addUrl(SitemapUrl $url)
  {
    $this->urls[] = $url;
  }

  /**
   * @return SitemapUrl[]
   */
  public function getUrls()
  {
    return $this->urls;
  }
}