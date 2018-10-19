<?php
/**
 * Created by PhpStorm.
 * User: eobuh
 * Date: 07.05.2018
 * Time: 18:40
 */

namespace Accurateweb\TaxonomyBundle\Model\Resolver;

use AccurateCommerce\Model\Taxonomy\TaxonInterface;
use Accurateweb\TaxonomyBundle\Exception\TaxonNotFoundException;

interface TaxonomyResolverInterface
{
  /**
   * @param $criteria mixed
   * @throws TaxonNotFoundException
   * @return TaxonInterface
   */
  public function resolve ($criteria);

  /**
   * @param $criteria mixed
   * @return boolean
   */
  public function supports ($criteria);

}