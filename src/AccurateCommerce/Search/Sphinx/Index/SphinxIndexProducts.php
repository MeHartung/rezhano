<?php

/*
 *  @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */

namespace AccurateCommerce\Search\Sphinx\Index;

use AccurateCommerce\Search\SphinxSourceType;

/**
 * Описывает индекс "Товары"
 *
 * @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
class SphinxIndexProducts extends SphinxIndexBase
{
  public function __construct()
  {
    parent::__construct(SphinxSourceType::SRC_PRODUCTS, Product::class, 'productIndex');
  }
}
