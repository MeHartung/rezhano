<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 06.09.2017
 * Time: 18:56
 */

namespace AccurateCommerce\Store\Catalog\Filter;

use AccurateCommerce\Store\Catalog\Filter\EavChoiceFilterField;
use AccurateCommerce\Store\Catalog\Filter\FilterField;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormBuilderInterface;

class EavFilter extends FilterField
{
  private $fields;

  public function buildForm(FormBuilderInterface $formBuilder)
  {
    foreach ($this->fields as $field)
    {
      $field->buildForm($formBuilder);
    }
  }

  public function __construct($attributes)
  {
    $this->fields = array();
    
    foreach ($attributes as $productAttribute)
    {
      $this->addField(new EavChoiceFilterField($productAttribute['id'], 'pav_'.$productAttribute['id'], array(
        'label' => $productAttribute['name']
      )));
    }
  }

  public function addField(EavChoiceFilterField $field)
  {
    $this->fields[$field->getId()] = $field;
  }

  /**
   *
   * @param QueryBuilder $queryBuilder
   */
  public function apply($queryBuilder)
  {
    foreach ($this->fields as $field)
    {
      $fieldValue = $field->getValue();

      if (!is_array($fieldValue))
      {
        $fieldValue = array($fieldValue);
      }

      $queryBuilder
        ->andWhere($queryBuilder->expr()->in('p.productAttributeValues', $fieldValue));

//      $alias = 'pavtp'.$field->getId();
//
//      $queryBuilder
//        ->innerJoin('p.productAttributeValuesToProducts', $alias)
//        ->andWhere($queryBuilder->expr()->in($alias.'.productAttributeValue', $fieldValue));
    }
  }

  protected function getWidgetId()
  {
    return null;
  }


  public function getFields()
  {
    return $this->fields;
  }

}