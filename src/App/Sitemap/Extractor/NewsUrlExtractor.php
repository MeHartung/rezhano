<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 27.09.2017
 * Time: 19:39
 */

namespace App\Sitemap\Extractor;

use StoreBundle\Entity\Text\Article;
use StoreBundle\Entity\Text\News;
use Symfony\Component\Routing\RouterInterface;

class NewsUrlExtractor extends SluggableUrlExtractor
{
  public function __construct(RouterInterface $router)
  {
    parent::__construct($router, 'news_show', 'weekly', 0.85);
  }

  public function supports($model)
  {
    return $model instanceof News;
  }
}