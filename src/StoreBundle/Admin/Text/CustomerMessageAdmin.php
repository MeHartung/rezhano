<?php

namespace StoreBundle\Admin\Text;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;

class CustomerMessageAdmin extends AbstractAdmin
{
  protected function configureFormFields (FormMapper $form)
  {
    $form
      ->add('createdAt', null, [
        'disabled' => true,
        'widget' => 'single_text',
        'format' => 'd.M.Y H:m',
      ])
      ->add('userName', null, [
        'disabled' => true,
      ])
      ->add('message', 'StoreBundle\Form\TinyMceType', [
        'custom_buttons' => [
          'quote' => [
            'label' => 'Цитата',
            'icon' => 'blockquote',
          ]
        ],
      ]);
  }
}