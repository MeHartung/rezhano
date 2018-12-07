<?php

namespace StoreBundle\Twig;

use Doctrine\ORM\EntityManagerInterface;
use StoreBundle\Entity\Store\Payment\Method\PaymentMethod;
use StoreBundle\Entity\Store\Shipping\ShippingMethod;

class CheckoutExtension extends \Twig_Extension
{
  private $em;
  
  public function __construct(EntityManagerInterface $entityManager)
  {
    $this->em = $entityManager;
  }
  
  public function getFilters()
  {
    return array(
      new \Twig_SimpleFilter('render_shipping_label', [$this, 'renderValueLabel'], [
        'needs_environment' => true,
      ]),
      new \Twig_SimpleFilter('render_shipping_info', [$this, 'renderValueInfo'], [
        'needs_environment' => true,
      ]),
      new \Twig_SimpleFilter('render_payment_label', [$this, 'renderPaymentLabel'], [
        'needs_environment' => true,
      ]),
      new \Twig_SimpleFilter('render_payment_info', [$this, 'renderPaymentInfo'], [
        'needs_environment' => true,
      ]),
    );
  }
  
  public function renderValueLabel(\Twig_Environment $twig, $valueId)
  {
    $value = $this->em->getRepository(ShippingMethod::class)->find($valueId);
    
    if(!$value) return null;
    
    return $twig->render('@Store/Checkout/patrial/shipping_label.html.twig', [
      'value' => $value
    ]);
  }
  public function renderValueInfo(\Twig_Environment $twig, $valueId, $total)
  {
    $value = $this->em->getRepository(ShippingMethod::class)->find($valueId);

    if(!$value) return null;
    
    return $twig->render('@Store/Checkout/patrial/shipping_info.html.twig', [
      'value' => $value,
      'total' => $total
    ]);
  }
  
  public function renderPaymentLabel(\Twig_Environment $twig, $valueId)
  {
    $value = $this->em->getRepository(PaymentMethod::class)->find($valueId);
    
    if(!$value) return null;
    
    return $twig->render('@Store/Checkout/patrial/payment_label.html.twig', [
      'value' => $value
    ]);
  }
  
  public function renderPaymentInfo(\Twig_Environment $twig, $valueId)
  {
    $value = $this->em->getRepository(PaymentMethod::class)->find($valueId);
    
    if(!$value) return null;
    
    return $twig->render('@Store/Checkout/patrial/payment_info.html.twig', [
      'value' => $value
    ]);
  }
  
}