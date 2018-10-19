<?php

namespace Accurateweb\SettingBundle\Admin;

use Accurateweb\SettingBundle\Model\Manager\SettingManagerInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class SettingsAdmin extends AbstractAdmin
{
  protected $settingManager;

  public function configureRoutes(RouteCollection $collection)
  {
    $collection->remove('create');
    $collection->remove('delete');
    parent::configureRoutes($collection);
  }

  public function __construct ($code, $class, $baseControllerName, SettingManagerInterface $settingManager)
  {
    $this->settingManager = $settingManager;
    parent::__construct($code, $class, $baseControllerName);
  }

  public function toString ($object)
  {
    $setting = $this->settingManager->getSetting($object->getName());
    return $setting->getDescription();
  }


  public function configureListFields(ListMapper $list)
  {
    $list
      ->add('name', null, array(
        'label' => 'Имя'
      ))
      ->add('value', null, array(
        'label' => 'Значение'
      ))
      ->add('comment', null, array(
        'label' => 'Описание'
      ))
      ->add('_action', null, array(
        'actions' => array(
          'edit' => array(),
        )
      ));
  }

  public function configureFormFields(FormMapper $form)
  {
    $subject = $this->getSubject();
    $setting = $this->settingManager->getSetting($subject->getName());
    $options = $setting->getFormOptions();

    $options = array_replace(array(
      'label' => 'Значение'
    ), $options);

//    if (!isset($options['data']))
//    {
//      $options['data'] = $setting->getValue();
//    }

    $form->add('value', $setting->getFormType(), $options);

    $data_transformer = $setting->getModelTransformer();

    if ($data_transformer)
    {
      $form->get('value')->addModelTransformer($data_transformer);
    }
  }

  public function getTemplate($name)
  {
    $template = parent::getTemplate($name);
    return $template;
  }

  public function preUpdate ($object)
  {
    $setting = $this->settingManager->getSetting($object->getName());
    $setting->setValue($object->getValue());
    //Вообще нужен свой modelmanager
  }

}