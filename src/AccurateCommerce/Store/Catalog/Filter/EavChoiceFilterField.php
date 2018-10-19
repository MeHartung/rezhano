<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 05.09.2017
 * Time: 16:18
 */

namespace AccurateCommerce\Store\Catalog\Filter;

use AccurateCommerce\Store\Catalog\Filter\Applicator\EavFilterApplicator;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class EavChoiceFilterField extends FilterField
{
  private $choices;

  private $attributeId;

  private $enabledChoices;

  public function __construct($attributeId, $id, array $options = array())
  {
    $this->choices = array();
    $this->enabledChoices = array();

    $this->attributeId = $attributeId;

    parent::__construct($id, $options);
  }

  public function buildForm(FormBuilderInterface $formBuilder)
  {
    $formBuilder->add($this->getId(), ChoiceType::class, array(
      'choices' => $this->choices,
      'expanded' => true,
      'multiple' => true,
      'label' => $this->options['label']
    ));
  }

  public function setup($queryBuilder)
  {
    $this->enabledChoices = $this->choices = $this->evaluate($queryBuilder);
  }

  public function adjust($queryBuilder)
  {
    $this->enabledChoices = $this->evaluate($queryBuilder);
  }

  /**
   * @param QueryBuilder $queryBuilder
   */
  protected function evaluate($queryBuilder)
  {
    $queryBuilder
      ->select('pav.id', 'pav.value')
      ->innerJoin('p.productAttributeValues', 'pav')
      ->andWhere($queryBuilder->expr()->eq('pav.productAttribute', $this->attributeId))
      ->orderBy('pav.value')
      ->groupBy('pav.id');

    $values = $queryBuilder->getQuery()->getArrayResult();

    $choices = array();
    foreach ($values as $value)
    {
      $choices[(string)$value['value']] = $value['id'];
    }

    return $choices;
  }

  public function apply($queryBuilder)
  {
    $fieldValue = $this->getValue();
    if ($fieldValue && !is_array($fieldValue))
    {
      $fieldValue = array($fieldValue);
    }

    if ($fieldValue && !empty($fieldValue))
    {
      $queryBuilder
        ->andWhere($queryBuilder->expr()->in('p.productAttributeValues', $fieldValue));
//      $alias = 'pavtp' . $this->getId();
//
//      $queryBuilder
//        ->innerJoin('p.productAttributeValuesToProducts', $alias)
//        ->andWhere($queryBuilder->expr()->in($alias . '.productAttributeValue', $fieldValue));
    }
  }

  protected function getWidgetId()
  {
    return 'choice_expanded';
  }

  public function getState()
  {
    if (empty($this->choices))
    {
      return null;
    }

    $choices = array();

    foreach ($this->choices as $value => $key)
    {
      $choices[] = array(
        'id' => $key,
        'enabled' => in_array($key, $this->enabledChoices),
        'value' => (string)$value
      );
    }

    $result = array(
      'state' => array(
        'choices' => $choices
      ),
      'value' => $this->getValue()
    );

    return $result;
  }
}