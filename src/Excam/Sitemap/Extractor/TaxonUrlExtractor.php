<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 27.09.2017
 * Time: 19:34
 */

namespace Excam\Sitemap\Extractor;


use StoreBundle\Entity\Store\Catalog\Taxonomy\Taxon;
use Symfony\Component\Routing\RouterInterface;

class TaxonUrlExtractor extends SluggableUrlExtractor
{
  public function __construct(RouterInterface $router)
  {
    parent::__construct($router, 'taxon', 'weekly', 0.85);
  }

  public function supports($model)
  {
    return $model instanceof Taxon;
  }
}