<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 27.09.2017
 * Time: 19:35
 */

namespace Excam\Sitemap\Extractor;


use StoreBundle\Entity\Store\Catalog\Product\Product;
use Symfony\Component\Routing\RouterInterface;

class ProductUrlExtractor extends SluggableUrlExtractor
{
  public function __construct(RouterInterface $router)
  {
    parent::__construct($router, 'product', 'weekly', 0.69);
  }

  public function supports($model)
  {
    return $model instanceof Product;
  }
}