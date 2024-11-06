<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 26.09.2017
 * Time: 19:05
 */

namespace Accurateweb\SeoBundle\Model\Sitemap;

class SitemapBuilder
{
  private $loaders = array();

  private $extractors = array();

  public function addLoader(SitemapLoaderInterface $loader, $priority=null)
  {
    $this->loaders[] = $loader;
  }

  public function addExtractor(SitemapUrlExtractorInterface $extractor, $priority=null)
  {
    $this->extractors[] = $extractor;
  }

  public function build()
  {
    $sitemap = new Sitemap();

    foreach ($this->loaders as $loader)
    {
      $models = $loader->load();

      foreach ($models as $model)
      {
        $extractor = $this->getUrlExtractor($model);

        if (!$extractor)
        {
          throw new SitemapBuilderException('Extractor not found');
        }

        $url = $extractor->extract($model);

        $sitemap->addUrl($url);
      }
    }

    return $sitemap;
  }

  private function getUrlExtractor($model)
  {
    foreach ($this->extractors as $extractor)
    {
      if ($extractor->supports($model))
      {
        return $extractor;
      }
    }

    return null;
  }
}