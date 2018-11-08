<?php

namespace StoreBundle\Admin\Store\Shipping;

use AccurateCommerce\Shipping\Method\Store\ShippingMethodStorePickup;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use StoreBundle\Entity\Store\Shipping\ShippingMethod;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvents;

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
      ->add('city')
      ->add('_action', null, array(
          'actions' => array(
            'edit' => null,
            'move' => array(
              'template' => 'PixSortableBehaviorBundle:Default:_sort_drag_drop.html.twig'
            ),
            'delete' => null
          )
        )
      );
  }
  
  public function configureFormFields(FormMapper $form)
  {
    /** @var ShippingMethod $subject */
    $subject = $this->getSubject();
    
    $form
      ->tab('Основное')
      ->add('name')
      ->add('cost', null, [
        'help' => 'Цена доставки. Выводится у метода доставки жёлтым цветом. Пример: "Доставка курьером по Екатеринбургу / от 300 рублей"'
      ])
     /* ->add('freeDeliveryThreshold', null, [
        'help' => 'Начиная с этой цены доставка становится бесплатной'
      ])*/

     ->add('city', 'choice', [
       'label' => 'Город',
       'required' => true,
       'choices' => [
         'Екатеринбург' => 'Екатеринбург',
         'Реж' => 'Реж',
         'Другой город' => 'Другой город'
       ]
     ])
      ->add('help')
      ->add('uid', ChoiceType::class, [
        'choices' => $this->getShippingChoices(),
        'label' => 'Тип'
      ])
      ->end()
      ->end();
    
    if ($this->getSubject()->getUid() === ShippingMethodStorePickup::UID)
    {
      $form
        ->tab('Адрес')
        ->add('address', null, [
          'help' => 'Полный адрес точки самовывоза, в формате: Россия, Свердловская область, Екатеринбург, Красноармейская улица, 68'
        ])
        ->add('showAddress', null, [
          'help' => 'Адрес, который увидит покупатель, будет подставлен в имя метода доставки.'
        ])

        ->end()
        ->end()
        ->getFormBuilder()->addEventListener(FormEvents::POST_SUBMIT,
          function (\Symfony\Component\Form\FormEvent $event) use ($subject)
          {
            $form = $event->getForm();
      
            if(!$subject->getAddress() && !$subject->getShowAddress() && $form->getData()->getUid() === ShippingMethodStorePickup::UID)
            {
              $form->addError(new FormError('Для того, чтобы сохранить, укажите адрес и адрес, который увидит покупатель'));
            }
          });
    }
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
    $collection->add('move', $this->getRouterIdParameter() . '/move/{position}');
  }
}