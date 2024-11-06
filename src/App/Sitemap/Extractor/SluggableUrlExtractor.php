<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 27.09.2017
 * Time: 19:07
 */

namespace App\Sitemap\Extractor;


use Accurateweb\SeoBundle\Model\Sitemap\SitemapUrl;
use Accurateweb\SeoBundle\Model\Sitemap\SitemapUrlExtractorInterface;
use StoreBundle\Sluggable\SluggableInterface;
use Symfony\Component\Routing\RouterInterface;

class SluggableUrlExtractor implements SitemapUrlExtractorInterface
{
  private $router;

  private $routeName;

  private $routeParameters;

  private $changefreq;

  private $priority;

  public function __construct(RouterInterface $router, $routeName, $changefreq, $priority, array $routeParameters = array())
  {
    $this->router = $router;
    $this->routeName = $routeName;
    $this->routeParameters = $routeParameters;
    $this->changefreq = $changefreq;
    $this->priority = $priority;
  }

  public function supports($model)
  {
    return $model instanceof SluggableInterface;
  }

  public function extract($model)
  {
    /* @var $model \AccurateCommerce\Model\Taxonomy\StaticTaxon */
    return new SitemapUrl(
      $this->router->generate($this->routeName, array_merge($this->routeParameters, array('slug' => $model->getSlug())),
          RouterInterface::ABSOLUTE_URL),
      $this->changefreq,
      $this->priority
    );
  }
}