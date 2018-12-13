<?php

namespace StoreBundle\Admin\Store;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use StoreBundle\Entity\Store\Store;
use Symfony\Component\Validator\Constraints\NotBlank;

class StoreAdmin extends AbstractAdmin
{
  protected $translationDomain = 'messages';

  protected function configureFormFields (FormMapper $form)
  {
    $form
      ->tab('Основное')
        ->add('name', null, [
          'required' => true,
          'constraints' => [
            new NotBlank(),
          ]
        ])
        //      ->add('description')
        ->add('address')
        ->add('phone', null, [
          'attr' => [
            'class' => 'inputmask',
            'data-inputmask' => "'mask': '+7 (999) 999-99-99'",
          ]
        ])
        //      ->add('email')
        ->add('workTime', 'Symfony\Component\Form\Extension\Core\Type\TextareaType')
        ->add('showFooter', null, [
          'label' => 'Отображать в футере',
        ])
        ->add('published')
        ->end()
      ->end()
      ->tab('Местоположение')
        ->add('longitude')
        ->add('latitude')
        ->add('fullAddress', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
          'label' => 'Полный адрес',
        ])
        ->add('teaser', 'Accurateweb\MediaBundle\Form\ImageType', [
          'label' => 'Превью карты',
          'image_id' => 'store'
        ])
        ->end()
      ->end();
  }

  protected function configureListFields (ListMapper $list)
  {
    $list
      ->add('name')
      ->add('address')
      ->add('phone')
      ->add('published', null, [
        'editable' => true,
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