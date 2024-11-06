<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace Accurateweb\LogisticBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;

class PickupPointAdmin extends AbstractAdmin
{
  protected function configureFormFields(FormMapper $form)
  {
    $form->add('postcode')
         ->add('address')
    ;
  }
}