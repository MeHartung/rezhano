<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace AccurateCommerce\Store\Catalog\Filter;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

abstract class FilterField
{
  private $id;

  private $label;

  private $applicator;

  private $evaluator;

  protected $options;

  protected $value;

  private $queryBuilder;

  public function __construct($id, array $options = array())
  {
    $this->id = $id;

    $resolver = new OptionsResolver();

    $resolver
      ->setDefaults(array(
        'enabled' => true,
        'show_label' => true,
        'collapsed' => false
      ))
      ->setRequired(array(
        'label'
      ));

    $this->configureOptions($resolver);

    $this->options = $resolver->resolve($options);
  }

  abstract public function apply($query);

  public function buildForm(FormBuilderInterface $formBuilder)
  {

  }

  public function configureOptions(Options $resolver)
  {

  }

  /**
   * Вызывается для получения граничных значений до применения остальных фильтров
   *
   * @param $queryBuilder
   */
  public function setup($queryBuilder)
  {

  }

  /**
   * Подстраивает поле под условие фильтра
   *
   * @param $queryBuilder
   */
  public function adjust($queryBuilder)
  {

  }

  /**
   * @return mixed
   */
  public function getId()
  {
    return $this->id;
  }


  /**
   * @return mixed
   */
  public function getLabel()
  {
    return $this->label;
  }

  /**
   * @param mixed $label
   */
  public function setLabel($label)
  {
    $this->label = $label;
  }

  public function getSchema()
  {
    return array(
      'label' => $this->options['label'],
      'showCollapsed' => $this->options['collapsed'],
      'widget' => $this->getWidgetId()
    );
  }

  abstract protected function getWidgetId();

  public function getState()
  {
    return array();
  }

  public function getValue()
  {
    return $this->value;
  }

  public function setValue($v)
  {
    $this->value = $v;
  }
}