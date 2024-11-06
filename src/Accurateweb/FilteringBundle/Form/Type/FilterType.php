<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 21.09.2017
 * Time: 19:55
 */

namespace Accurateweb\FilteringBundle\Form\Type;

use AccurateCommerce\Store\Catalog\Filter\BaseFilter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterType extends AbstractType
{
  public function buildView(FormView $view, FormInterface $form, array $options)
  {
    $view->vars = array_merge($view->vars, array(
      'taxon' => $options['filter']->getTaxon()
    ));
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setRequired(array(
      'filter'
    ))
    ->setAllowedTypes('filter', BaseFilter::class);
  }
}