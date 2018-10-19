<?php

namespace Accurateweb\TaxonomyBundle\Model\TaxonPresentation;


use AccurateCommerce\Store\Catalog\Filter\ProductFilter;

interface TaxonFilterableInterface
{
  /**
   * @return ProductFilter
   */
  public function getFilter();
}