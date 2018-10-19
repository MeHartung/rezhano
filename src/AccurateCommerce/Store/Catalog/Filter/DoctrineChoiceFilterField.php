<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 05.09.2017
 * Time: 10:40
 */

namespace AccurateCommerce\Store\Catalog\Filter;


use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class DoctrineChoiceFilterField extends FilterField
{
  private $choices;

  private $enabledChoices;

  public function __construct($id, array $options = array())
  {
    $this->choices = array();
    $this->enabledChoices = array();

    parent::__construct($id, $options);
  }

  public function buildForm(FormBuilderInterface $formBuilder)
  {
    if (!empty($this->choices))
    {
      $formBuilder->add($this->getId(), ChoiceType::class, array(
        'choices' => $this->choices,
        'expanded' => true,
        'multiple' => true,
        'label' => $this->options['label']
      ));
    }
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
      ->select('b.id', 'b.name')
      ->innerJoin('p.brand', 'b')
      ->orderBy('b.name')
      ->groupBy('p.brand');

    $brands = $queryBuilder->getQuery()->getArrayResult();

    $choices = array();
    foreach ($brands as $brand)
    {
      $choices[$brand['name']] = $brand['id'];
    }

    return $choices;
  }

  /**
   * @param QueryBuilder $query
   */
  public function apply($query)
  {
    if ($this->value)
    {
      $query
        ->innerJoin('p.brand', 'b')
        ->andWhere($query->expr()->in('b.id', $this->value));
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
        'value' => $value
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