<?php

namespace Accurateweb\TaxonomyBundle\Model\TaxonPresentation;

use AccurateCommerce\Sort\ProductSortInterface;

interface TaxonSortableInterface
{
  /**
   * @return ProductSortInterface
   */
  public function getSort();
}