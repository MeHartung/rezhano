<?php

namespace AccurateCommerce\Model\Taxonomy;

use Doctrine\ORM\QueryBuilder;

/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */
interface TaxonInterface
{
  /**
   * @param string $alias
   * @return QueryBuilder
   */
  public function getProductQueryBuilder($alias='p');

  /**
   * @param QueryBuilder $queryBuilder
   * @return mixed
   */
  public function buildQuery(QueryBuilder $queryBuilder);

  /**
   * @return string
   */
  public function getName();

  /**
   * @return array
   */
  public function getChildren();

  /**
   * @return object
   */
  public function getTaxonEntity();

  /**
   * @return int|string|null
   */
  public function getId();

  /**
   * @return string
   */
  public function getShortName();

  /**
   * @return string
   */
  public function getDescription();

  /**
   * @return string
   */
  public function getSlug();

  /**
   * @return string
   */
  public function getRouteName();

  /**
   * @return array
   */
  public function getUrlParameters();

  /**
   * @return mixed
   */
  public function getPresentationOptions();
}