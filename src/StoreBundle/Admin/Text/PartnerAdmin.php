<?php

namespace StoreBundle\Admin\Text;


use Accurateweb\MediaBundle\Form\ImageType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use StoreBundle\Entity\Text\Partner;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvents;

class PartnerAdmin extends AbstractAdmin
{
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
      );
  }
  
  public function configureFormFields(FormMapper $form)
  {
    $subject = $this->getSubject();
    
    $form
      ->add('name')
      ->add('teaser', ImageType::class);
    
    $form->getFormBuilder()->addEventListener(FormEvents::POST_SUBMIT,
      function (\Symfony\Component\Form\FormEvent $event) use ($subject)
      {
        if ($this->getSubject()->getTeaser() === null)
        {
          $text = 'Нельзя создать партнёра не прикрепив изображение';
          $error = new FormError($text);
          $event->getForm()->get('teaser')->addError($error);
        }
      });
  }
  
  protected function configureRoutes(RouteCollection $collection)
  {
    $collection->add('move', $this->getRouterIdParameter() . '/move/{position}');
  }
}