<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace AccurateCommerce\Store\Catalog\Filter;


use Doctrine\ORM\QueryBuilder;

interface FilterApplicatorInterface
{
  /**
   * @param QueryBuilder $queryBuilder
   * @param $value
   * @return mixed
   */
  public function apply($queryBuilder, $value);
}