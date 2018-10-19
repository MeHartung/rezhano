<?php

/*
 *  @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */

namespace AccurateCommerce\Search\Sphinx\Index;

use AccurateCommerce\Search\SphinxSourceType;
use StoreBundle\Entity\Store\Catalog\Taxonomy\Taxon;

/**
 * Описывает индекс Sphinx "Разделы каталога"
 *
 * @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
class SphinxIndexCatalogSections extends SphinxIndexBase
{
  /**
   * Конструктор.
   */
  public function __construct()
  {
    parent::__construct(SphinxSourceType::SRC_CATALOG_SECTIONS, Taxon::class, 'taxonIndex');
  }
}
