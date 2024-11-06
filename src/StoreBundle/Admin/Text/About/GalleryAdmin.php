<?php


namespace StoreBundle\Admin\Text\About;


use Accurateweb\MediaBundle\Form\ImageGalleryType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class GalleryAdmin extends AbstractAdmin
{
  public function configureListFields(ListMapper $list)
  {
    $list
      ->add("title")
      ->add('_action', null, [
        'actions' => [
          'edit' => [],
        ]]);
  }
  
  public function configureFormFields(FormMapper $form)
  {
    $form
      ->add("title");
    
    if($this->subject->getId())
    {
      $form->add('images', ImageGalleryType::class, array(
      'gallery' => 'about-image',
      'label' => false,
      'crop' => [
        'size' => '570x713', //Размеры поля кропа (отношение сторон будет сохранено)
        'boxWidth' => 800, //Размеры модального окна
        'boxHeight' => 600
      ]
    )) ;
    }
  }
  
  public function configureRoutes(RouteCollection $collection)
  {
    $collection->remove('create');
    $collection->remove('delete');
  }
}