<?php

namespace StoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class ExcursionTeaserAdmin extends AbstractAdmin
{
  protected function configureFormFields (FormMapper $form)
  {
    $form
      ->add('title')
      ->add('description')
      ->add('teaser', 'Accurateweb\MediaBundle\Form\ImageType', [
        'label' => 'Иконка',
        'required' => true,
        'image_id' => 'excursion_teaser/teaser',
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
            'delete' => null
          )
        )
      );
  }
}