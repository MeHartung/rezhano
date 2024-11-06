<?php

namespace StoreBundle\Admin\Document;


use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class RegistrationDocumentAdmin extends AbstractAdmin
{
  protected $translationDomain = 'messages';

  protected function configureFormFields (FormMapper $form)
  {
    $subject = $this->getSubject();
    $fileOptions = [
      'data' => null,
    ];

    if ($subject->getFile())
    {
      $fileOptions['help'] = $subject->getFile();
    }

    $form
      ->add('name')
      ->add('file', 'Symfony\Component\Form\Extension\Core\Type\FileType', $fileOptions)
      ->add('show');
  }

  protected function configureListFields (ListMapper $list)
  {
    $list
      ->add('name')
      ->add('show')
      ->add('createdAt', 'date', [
        'format' => 'd.m.Y',
      ])
      ->add('_action', null, array(
        'actions' => array(
          'edit' => array(),
          'delete' => array(),
        )
      ));
  }
}