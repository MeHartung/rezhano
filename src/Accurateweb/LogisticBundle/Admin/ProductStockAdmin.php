<?php

namespace Accurateweb\LogisticBundle\Admin;

use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use StoreBundle\Entity\Store\Logistics\Warehouse\ProductStock;

class ProductStockAdmin extends AbstractAdmin
{
  protected function configureFormFields (FormMapper $form)
  {
    /** @var ProductStock $subject */
    $subject = $this->getSubject();
    $isEdit = $subject && $subject->getProduct() && $subject->getWarehouse();

    $form
      ->add('warehouse', null, [
        'disabled' => $isEdit,
      ])
      ->add('city', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
        'mapped' => false,
        'data' => $isEdit?$subject->getWarehouse()->getCity()->getName():null,
        'disabled' => true,
        'label' => 'Город',
      ])
      ->add('value')
      ->add('reservedValue', 'Symfony\Component\Form\Extension\Core\Type\IntegerType', [

      ]);
  }

  protected function configureListFields (ListMapper $list)
  {
    $list
      ->add('warehouse')
      ->add('value');
  }

}