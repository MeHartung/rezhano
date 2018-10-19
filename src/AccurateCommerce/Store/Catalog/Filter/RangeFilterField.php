<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace AccurateCommerce\Store\Catalog\Filter;


use Accurateweb\FilteringBundle\Form\Type\PriceRangeFilterType;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormBuilderInterface;

class RangeFilterField extends FilterField
{
  private $range;

  public function __construct($id, array $options = array())
  {
    $this->range = array(
      'min' => 0,
      'max' => 0
    );

    $this->value = array(
      'min' => null,
      'max' => null
    );

    parent::__construct($id, $options);
  }

  public function buildForm(FormBuilderInterface $formBuilder)
  {
    $formBuilder->add($this->getId(), PriceRangeFilterType::class, array(
      'range' => $this->range
    ));
  }

  public function setup($queryBuilder)
  {
    $this->range = $this->evaluate($queryBuilder);
  }

  public function adjust($queryBuilder)
  {
    $this->range = $this->evaluate($queryBuilder);
  }

  /**
   * @param QueryBuilder $queryBuilder
   * @return array
   */
  public function evaluate($queryBuilder)
  {
    $range = array('min' => null, 'max' => null);

    $queryBuilder->select(array(
      $queryBuilder->expr()->min('p.price'),
      $queryBuilder->expr()->max('p.price')
    ));

    $result = $queryBuilder->getQuery()->getArrayResult();

    $multiplier = 100 < $result[0][1] ? 100 : 10;
    $range['min'] = floor($result[0][1] / $multiplier) * $multiplier;

    $multiplier = 100 < $result[0][2] ? 100 : 10;
    $range['max'] = ceil($result[0][2] / $multiplier) * $multiplier;

    return $range;
  }

  /**
   * @param QueryBuilder $query
   */
  public function apply($query)
  {
    if ($this->value['min'])
    {
      $query->andWhere($query->expr()->gte('p.price', $this->value['min']));
    }

    if ($this->value['max'])
    {
      $query->andWhere($query->expr()->lte('p.price', $this->value['max']));
    }
  }

  public function getSchema()
  {
    return array_merge(
      parent::getSchema(),
      array('units' => '₽')
    );
  }

  protected function getWidgetId()
  {
    return 'range_slider';
  }

  public function getState()
  {
    $value = $this->value;
    if (null === $value['min'] && null === $value['max'])
    {
      $value = null;
    }

    return array(
      'state' => array(
        'limits' => array(
          'min' => $this->range['min'],
          'max' => $this->range['max']
        )
      ),
      'value' => $value
    );
  }
}