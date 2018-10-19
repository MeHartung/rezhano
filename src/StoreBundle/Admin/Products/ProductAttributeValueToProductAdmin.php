<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 16.08.17
 * Time: 15:07
 */

namespace StoreBundle\Admin\Products;


use Doctrine\DBAL\Types\TextType;
use StoreBundle\Entity\Store\Catalog\Product\Attributes\ProductAttribute;
use StoreBundle\Entity\Store\Catalog\Product\Attributes\ProductAttributeValue;
use StoreBundle\Form\ProductAttributeValueToProductType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;


class ProductAttributeValueToProductAdmin extends AbstractAdmin
{

 public function configureListFields(ListMapper $list)
 {
   $list
    ->add('id')
     ->add('product')
    ->add('_action', null, [
      'actions' => [
      'edit' => []
    ]])
     ;
 }

  public function configureFormFields(FormMapper $form)
  {
    $subject = $this->getSubject();
    $form
     ->add('product')
      ->add('productAttributeValue', ProductAttributeValueToProductType::class);
    /*if ($subject->getId() !== null) {
      $form->getFormBuilder()->addEventListener(FormEvents::PRE_SET_DATA,

        function (FormEvent $event) use ($form, $subject)
        {
          $form
        //    ->add('product')
            ->add('productAttributeValue', ProductAttributeValueToProductType::class);
        });
       }*/
  }

}