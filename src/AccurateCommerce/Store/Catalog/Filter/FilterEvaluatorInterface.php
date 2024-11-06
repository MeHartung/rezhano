<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace AccurateCommerce\Store\Catalog\Filter;


interface FilterEvaluatorInterface
{
  /**
   * @param $queryBuilder
   *
   * @return mixed
   */
  public function evaluate($queryBuilder);
}