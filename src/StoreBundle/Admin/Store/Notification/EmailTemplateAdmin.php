<?php


namespace StoreBundle\Admin\Store\Notification;

use Accurateweb\EmailTemplateBundle\Admin\EmailTemplateAdmin as Base;
use Sonata\AdminBundle\Form\FormMapper;
use StoreBundle\Form\TinyMceType;

class EmailTemplateAdmin extends Base
{
  public function configureFormFields(FormMapper $form)
  {
    $form
      ->add('SupportedVariables', 'Accurateweb\\EmailTemplateBundle\\Form\\Type\\SupportedVariablesType', array('label' => 'Доступные переменные'))
      ->add('Subject', 'Symfony\\Component\\Form\\Extension\\Core\\Type\\TextType', array('label' => 'Шаблон темы письма'))
      ->add('Body', TinyMceType::class, array(
        'label' => 'Шаблон тела письма',
      ));
  }
}