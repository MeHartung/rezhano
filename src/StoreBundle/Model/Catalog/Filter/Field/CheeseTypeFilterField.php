<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Model\Catalog\Filter\Field;

use AccurateCommerce\Store\Catalog\Filter\DoctrineChoiceFilterField;
use Accurateweb\SettingBundle\Model\Manager\SettingManagerInterface;

class CheeseTypeFilterField extends DoctrineChoiceFilterField
{

  private $settingsManager;

  public function __construct($id, array $options = array(), SettingManagerInterface $settingManager)
  {
    $this->settingsManager = $settingManager;
    parent::__construct($id, $options);
  }

  /**
   * @param $query
   */
  public function applyForAttribute($query, $alias, $values)
  {
    if (is_array($values) && !empty($values)) {
      $query
        ->innerJoin('p.productAttributeValues', $alias)
        ->andWhere($query->expr()->in($alias.'.id', $values));
    }
  }

  protected function evaluate($queryBuilder)
  {
    $cheeseFirmness = (clone $queryBuilder)
      ->select('pav.id', 'pav.value')
      ->innerJoin('p.productAttributeValues', 'pav')
      ->orderBy('pav.value')
      ->andWhere('IDENTITY(pav.productAttribute) = :productAttributeId')
      ->setParameter('productAttributeId', $this->settingsManager->getSetting('cheese_hardness_property')->getValue()->getId())
      ->getQuery()
      ->getResult();

    $cheeseMolds = (clone $queryBuilder)
      ->select('pav.id', 'pav.value')
      ->innerJoin('p.productAttributeValues', 'pav')
      ->orderBy('pav.value')
      ->andWhere('IDENTITY(pav.productAttribute) = :productAttributeId')
      ->setParameter('productAttributeId',  $this->settingsManager->getSetting('cheese_mold_property')->getValue()->getId())
      ->getQuery()
      ->getResult();

    $choices =  [];
    foreach ($cheeseFirmness as $cheeseFirmnessValue)
    {
      $choices[$cheeseFirmnessValue['value']] = 'f'.$cheeseFirmnessValue['id'];
    }

    if (!empty($cheeseMolds))
    {
      $choices['С плесенью'] = 'mold';

      foreach ($cheeseMolds as $cheeseMold)
      {
        $choices['— ' . $cheeseMold['value']] = 'm'.$cheeseMold['id'];
      }
    }

    return $choices;
  }

  public function apply($query)
  {
    if ($this->value)
    {
      $fValues = [];
      $mValues = [];

      foreach ($this->value as $val)
      {
        if ($val !== 'mold')
        {
          $t = $val[0];

          switch ($t)
          {
            case 'f':
              $fValues[] = substr($val, 1);
              break;
            case 'm':
              $mValues[] = substr($val, 1);
              break;
          }
        }
      }

      $this->applyForAttribute($query, 'pavf', $fValues);
      $this->applyForAttribute($query, 'pavm', $mValues);
    }
  }
}