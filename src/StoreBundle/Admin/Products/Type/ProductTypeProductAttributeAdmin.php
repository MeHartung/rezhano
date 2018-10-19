<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 15.08.17
 * Time: 12:40
 */

namespace StoreBundle\Admin\Products\Type;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Admin\AbstractAdmin;

class ProductTypeProductAttributeAdmin extends AbstractAdmin
{

  public function configureFormFields(FormMapper $form)
  {
    $form
      ->add('productType')
      ->add('productAttribute')
      ;
  }

}