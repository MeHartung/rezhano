<?php

namespace Accurateweb\TaxonomyBundle\Model\TaxonPresentation;

use AccurateCommerce\Pagination\Pagination;

interface TaxonPaginationInterface
{
  /**
   * @return Pagination
   */
  public function getPagination();
}