<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 26.09.2017
 * Time: 18:38
 */

namespace Accurateweb\SeoBundle\Model\Sitemap;

class SitemapUrl
{
  private $url;

  private $changefreq;

  private $priority;

  public function __construct($url, $changefreq, $priority)
  {
    $this->url = $url;
    $this->changefreq = $changefreq;
    $this->priority = $priority;
  }

  /**
   * @return mixed
   */
  public function getUrl()
  {
    return $this->url;
  }

  /**
   * @return mixed
   */
  public function getChangefreq()
  {
    return $this->changefreq;
  }

  /**
   * @return mixed
   */
  public function getPriority()
  {
    return $this->priority;
  }


}