<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Twig;


use StoreBundle\Entity\Store\Catalog\Product\Product;
use StoreBundle\Entity\Store\Order\OrderItem;
use StoreBundle\Service\Product\ProductPrice\ProductPriceManager;

class CurrencyExtension extends \Twig_Extension
{
  private $priceManager;

  public function __construct (ProductPriceManager $priceManager)
  {
    $this->priceManager = $priceManager;
  }

  public function getFilters()
  {
    return array(
      new \Twig_SimpleFilter('price', array($this, 'priceFilter'), array('is_safe' => array('html' => true))),
      new \Twig_SimpleFilter('productPrice', array($this, 'productPriceFilter'), array('is_safe' => array('html' => true))),
    );
  }

  public function priceFilter($value, $decimals=0, $decPoint=',', $thousandsSep=' ')
  {
    $price = number_format(floor($value), $decimals, $decPoint, $thousandsSep);
    $cops = round($value - floor($value), 2) * 100;
    $price .= sprintf(',<span class="payment-info__value_fraction">%02d</span>', $cops);
    $price = $price.' ₽';

    return $price;
  }

  public function productPriceFilter($product, $decimals=0, $decPoint=',', $thousandsSep=' ')
  {
    if ($product instanceof OrderItem)
    {
      $product = $product->getProduct();
    }

    if (!$product instanceof Product)
    {
      throw new \InvalidArgumentException();
    }

    $price = $this->priceManager->getProductPrice($product);
    return $this->priceFilter($price, $decimals, $decPoint, $thousandsSep);
  }
}