<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 28.09.2017
 * Time: 11:30
 */

namespace App\Sitemap\Loader;


use Accurateweb\SeoBundle\Model\Sitemap\ArraySitemapLoader;
use Accurateweb\SeoBundle\Model\Sitemap\SitemapUrl;
use Symfony\Component\Routing\Router;

class StaticUrlLoader extends ArraySitemapLoader
{
  public function __construct(Router $router)
  {
    parent::__construct(array(
      new SitemapUrl($router->generate('news_index', array(), Router::ABSOLUTE_URL), 'weekly', 0.56),
      new SitemapUrl($router->generate('special_offers_index', array(), Router::ABSOLUTE_URL), 'weekly', 0.56),
      #new SitemapUrl($router->generate('contacts_index', array(), Router::ABSOLUTE_URL), 'weekly', 0.56),
      new SitemapUrl($router->generate('articles_index', array(), Router::ABSOLUTE_URL), 'weekly', 0.56),
    ));
  }
}