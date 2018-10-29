<?php

namespace StoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class HomeBannerAdmin extends AbstractAdmin
{
  protected function configureFormFields (FormMapper $form)
  {
    $form
      ->add('teaser', 'Accurateweb\MediaBundle\Form\ImageType', [
        'label' => 'Изображение',
        'required' => true,
        'image_id' => 'homepage-banner/teaser'
      ])
      ->add('textImageFile', 'Accurateweb\MediaBundle\Form\ImageType', [
        'label' => 'Текстовое изображение',
        'required' => true,
        'image_id' => 'homepage-banner/text'
      ])
      ->add('text')
      ->add('buttonLabel')
      ->add('url', null, [
        'required' => false,
        'constraints' => [
//          new Url()
        ]
      ])
      ->add('enabled');
  }

  protected function configureListFields (ListMapper $list)
  {
    $list
      ->add('image', null, [
        'template' => ':CRUD:image_list_field.html.twig',
      ])
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

  protected function configureDatagridFilters (DatagridMapper $filter)
  {
    parent::configureDatagridFilters($filter);
  }

  protected function configureRoutes(RouteCollection $collection)
  {
    $collection->add('move', $this->getRouterIdParameter().'/move/{position}');
  }

  public function configure()
  {
    $this->setTemplate('list', 'StoreBundle:SonataAdmin\CRUD:list_sortable.html.twig');
  }

}