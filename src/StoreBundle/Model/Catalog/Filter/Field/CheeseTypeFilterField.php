<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Model\Catalog\Filter\Field;

use AccurateCommerce\Store\Catalog\Filter\DoctrineChoiceFilterField;

class CheeseTypeFilterField extends DoctrineChoiceFilterField
{
  public function __construct($id, array $options = array())
  {
    parent::__construct($id, $options);
  }

  protected function evaluate($queryBuilder)
  {
    $cheeseFirmness = (clone $queryBuilder)
      ->select('pav.id', 'pav.value')
      ->innerJoin('p.productAttributeValues', 'pav')
      ->orderBy('pav.value')
      ->andWhere('IDENTITY(pav.productAttribute) = :productAttributeId')
      ->setParameter('productAttributeId', 1)
      ->getQuery()
      ->getResult();

    $cheeseMolds = (clone $queryBuilder)
      ->select('pav.id', 'pav.value')
      ->innerJoin('p.productAttributeValues', 'pav')
      ->orderBy('pav.value')
      ->andWhere('IDENTITY(pav.productAttribute) = :productAttributeId')
      ->setParameter('productAttributeId', 2)
      ->getQuery()
      ->getResult();

    $choices =  [];
    foreach ($cheeseFirmness as $cheeseFirmnessValue)
    {
      $choices[$cheeseFirmnessValue['id']] = $cheeseFirmnessValue['value'];
    }

    if (!empty($cheeseMolds))
    {
      $choices['mold'] = 'С плесенью';

      foreach ($cheeseMolds as $cheeseMold)
      {
        $choices[$cheeseMold['id']] = $cheeseMold['name'];
      }
    }

    return $choices;
  }
}