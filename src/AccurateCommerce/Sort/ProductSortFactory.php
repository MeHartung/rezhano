<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 25.09.2018
 * Time: 23:03
 */

namespace AccurateCommerce\Sort;

use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductSortFactory implements ProductSortFactoryInterface
{
  public function create(array $options = [])
  {
    $resolver = new OptionsResolver();
    $resolver->setRequired(['column', 'order']);

    $options = $resolver->resolve($options);

    return new ProductSort($options['column'], $options['order']);
  }
}