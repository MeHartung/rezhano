<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Twig;


use StoreBundle\Entity\Store\Catalog\Product\Product;
use StoreBundle\Entity\Store\Order\OrderItem;
use StoreBundle\Service\Product\ProductPrice\ProductPriceManager;

class QuantityExtension extends \Twig_Extension
{

  public function __construct ()
  {
  }

  public function getFilters()
  {
    return array(
      new \Twig_SimpleFilter('scaledQuantity', array($this, 'scaledQuantityFilter'), array('is_safe' => array('html' => true))),
      new \Twig_SimpleFilter('scaledQuantityUnits', array($this, 'scaledQuantityUnitsFilter'), array('is_safe' => array('html' => true))),
    );
  }

  public function scaledQuantityFilter(OrderItem $value)
  {

  }

  public function getScale(OrderItem $value) {

    $quantityScales = array(
      '0' => array(
          'units' => 'кг',
          'multiplicator' => 1
      ),
      '1' => array(
        'units' => 'г',
          'multiplicator' => 1000
      ),
    );

    if ($value->getProduct() && $value->getProduct()->getProductType() && $value->getProduct()->getProductType()->getMeasured())
    {

    }
  }

}