<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 31.08.2017
 * Time: 19:26
 */

namespace StoreBundle\Admin\Store\Order;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use StoreBundle\Entity\Store\Order\OrderItem;
use Knp\Menu\ItemInterface as MenuItemInterface;
use StoreBundle\Entity\Store\Catalog\Product\Product;
use StoreBundle\Entity\Store\Order\Order;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class OrderItemAdmin extends AbstractAdmin
{
  protected $parentAssociationMapping = 'order';

  protected function configureFormFields(FormMapper $form)
  {
    $form
      ->add('product', EntityType::class, array(
        'class' => Product::class,
        'choice_label' => 'name'
      ))
      ->add('price', TextType::class, array('disabled' => true))
      ->add('quantity')
      ->add('cost', TextType::class, array('disabled' => true))
      ;

  }

  protected function configureListFields(ListMapper $listMapper)
  {
    $listMapper
      ->add('product', EntityType::class, array(
        'class' => Product::class,
        'choice_label' => 'name'
      ))
      ->add('price', TextType::class, array('disabled' => true))
      ->add('quantity')
      ->add('cost', TextType::class, array('disabled' => true))
      ->add('_action', null, array(
        'actions' => [
          'edit' => [],
          'delete' => [],
        ]
      ));

  }

  protected function configureRoutes(RouteCollection $collection)
  {
    if ($this->isChild())
    {
      $collection->clearExcept(['show', 'edit', 'list', 'delete', 'create']);
      return;
    }

    // This is the route configuration as a parent
    $collection->clear();

  }

}
