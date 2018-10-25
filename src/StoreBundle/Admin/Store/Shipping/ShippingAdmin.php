<?php

namespace StoreBundle\Admin\Store\Shipping;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ShippingAdmin extends AbstractAdmin
{
  public function configureListFields(ListMapper $list)
  {
    $list
      ->add('name');
  }
  
  public function configureFormFields(FormMapper $form)
  {
    $form
      ->add('name')
      ->add('help')
      ->add('uid', ChoiceType::class, [
        'choices' => $this->getShippingChoices(),
        'label' => 'Тип'
      ]);
  }
  
  private function getShippingChoices()
  {
    $shippingManager = $this->getConfigurationPool()->getContainer()->get('accurateweb.shipping.manager');
    $choices = array();
    
    $shippingMethods = $shippingManager->getShippingMethods();
    
    foreach ($shippingMethods as $shippingMethod)
    {
     # $cityName = $this->getSubject()->getShippingCityName();
      
      $choices[$shippingMethod->getInternalName()] = $shippingMethod->getUid();
    }
    
    return $choices;
  }
}