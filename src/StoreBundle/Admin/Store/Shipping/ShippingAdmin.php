<?php

namespace StoreBundle\Admin\Store\Shipping;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ShippingAdmin extends AbstractAdmin
{
  protected $datagridValues = array(
    '_page' => 1,
    '_sort_order' => 'ASC',
    '_sort_by' => 'position',
  );
  
  public function configureListFields(ListMapper $list)
  {
    $list
      ->add('name')
      ->add('_action', null, array(
          'actions' => array(
            'edit' => null,
            'move' => array(
              'template' => 'PixSortableBehaviorBundle:Default:_sort_drag_drop.html.twig'
            ),
            'delete' => null
          )
        )
      );;
  }
  
  public function configureFormFields(FormMapper $form)
  {
    $form
      ->add('name')

      ->add('cost')
      ->add('freeDeliveryThreshold', null, [
        'help' => 'Начиная с этой цены доставка становится бесплатной'
      ])
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
  
  protected function configureRoutes(RouteCollection $collection)
  {
    $collection->add('move', $this->getRouterIdParameter().'/move/{position}');
  }
}