<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 25.09.2018
 * Time: 22:59
 */

namespace StoreBundle\Model\Product\Sort;

use AccurateCommerce\Sort\ProductSortFactoryInterface;
use Accurateweb\LocationBundle\Service\Location;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductSortFactory implements ProductSortFactoryInterface
{
  private $location;

  public function __construct(Location $location)
  {
    $this->location = $location;
  }

  public function create(array $options = [])
  {
    $resolver = new OptionsResolver();
    $resolver->setRequired(['column', 'order']);

    $options = $resolver->resolve($options);

    return new ProductSort($this->location, $options['column'], $options['order']);
  }


}