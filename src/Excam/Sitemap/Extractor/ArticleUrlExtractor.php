<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 27.09.2017
 * Time: 19:39
 */

namespace Excam\Sitemap\Extractor;

use StoreBundle\Entity\Text\Article;
use Symfony\Component\Routing\RouterInterface;

class ArticleUrlExtractor extends SluggableUrlExtractor
{
  public function __construct(RouterInterface $router)
  {
    parent::__construct($router, 'articles_show', 'weekly', 0.85);
  }

  public function supports($model)
  {
    return $model instanceof Article;
  }
}