<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Admin\Store\Order;

use Doctrine\ORM\QueryBuilder;
use StoreBundle\Entity\Store\Order\Status\OrderStatusHistory;
use StoreBundle\Entity\Store\Order\Status\OrderStatus;
use Knp\Menu\ItemInterface as MenuItemInterface;
use AccurateCommerce\Shipping\ShippingManager;
use Doctrine\ORM\EntityManagerInterface;
use StoreBundle\Entity\Store\Order\Order;
use StoreBundle\Entity\Store\Order\OrderItem;
use StoreBundle\Entity\Store\Payment\Method\PaymentMethod;
use StoreBundle\Form\Admin\Status\StatusType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class OrderAdmin extends AbstractAdmin
{
  protected $datagridValues = array(

    // display the first page (default = 1)
    '_page' => 1,

    // reverse order (default = 'ASC')
    '_sort_order' => 'DESC',

    // name of the ordered field (default = the model's id field, if any)
    '_sort_by' => 'createdAt',
  );

  /** @var $shippingManager ShippingManager */
  private $shippingManager;
  /** @var  $em EntityManagerInterface */
  private $em;

  public function setShippingManager(ShippingManager $shippingManager, EntityManagerInterface $em)
  {
    $this->shippingManager = $shippingManager;
    $this->em = $em;
  }

  protected function configureListFields(ListMapper $list)
  {
    $list->add('checkoutAt', 'datetime', array(
      'format' => 'd.m.Y H:i'
    ))
      ->addIdentifier('document_number')
      ->add('customer_full_name')
      ->add('customer_phone')
      ->add('customer_email')
      ->add('payment_method')
      ->add('shipping_method', null, array(
        'template' => 'StoreBundle:CRUD/Store/Order:shipping_method.html.twig'
      ))
      ->add('subtotal')
      ->add('shipping_cost')
      ->add('fee')
      ->add('total')
      ->add('checkout_state_name')
      ->add('orderAdminStatus', 'html', ['strip' => false])
      ->add('_action', null, array(
        'actions' => [
          'edit' => [
            'template' => 'StoreBundle:CRUD/Store/Order:list__action_edit.html.twig',
          ],
          'checkout' => [
            'template' => 'StoreBundle:CRUD/Store/Order:list__action_checkout.html.twig'
          ],
          'status' => [
            'template' => 'StoreBundle:CRUD/Store/Order:list__action_status.html.twig'
          ],
          'cancel' => [
            'template' => 'StoreBundle:CRUD/Store/Order:list__action_order_cancel.html.twig'
          ],
          'orderItems' => [
            'template' => 'StoreBundle:CRUD/Store/Order:list__action_order_items.html.twig'
          ],
          'orderHistory' => [
            'template' => 'StoreBundle:CRUD/Store/Order:list__action_order_status_history.html.twig'
          ],
        ]
      ));
  }

  public function createQuery($context = 'list')
  {
    /** @var QueryBuilder $query */
    $query = parent::createQuery($context);

    $query->andWhere(
      $query->expr()->eq($query->getRootAliases()[0] . '.checkoutStateId', Order::CHECKOUT_STATE_COMPLETE)
    );

    //$query->setSortBy()

    return $query;
  }

  protected function configureFormFields(FormMapper $form)
  {
    $form
      ->tab('Основные')
      ->add('document_number', TextType::class, array(
        'disabled' => true,
        'required' => false
      ))
      ->add('customer_first_name', TextType::class)
      ->add('customer_last_name', TextType::class)
      ->add('customer_phone', TextType::class)
      ->add('customer_email', EmailType::class)
      ->add('payment_method', EntityType::class, array(
        'class' => PaymentMethod::class
      ))
      ->add('shipping_method', ChoiceType::class, array(
        'choices' => $this->getShippingChoices()
      ))
      ->add('shipping_city_name', TextType::class)
      ->add('shipping_postcode', TextType::class)
      ->add('shipping_address', TextType::class)
      ->add('customer_comment', TextareaType::class)
      ->add('shippingCost', NumberType::class)
      ->add('fee', NumberType::class)
      ->add('total', TextType::class, array('disabled' => true))
      ->add('paymentStatus')
      ->end()
      ->end()
    ;
  }

  private function getShippingChoices()
  {
    $choices = array();

    $shippingMethods = $this->shippingManager->getShippingMethods();

    foreach ($shippingMethods as $shippingMethod)
    {
      $cityName = $this->getSubject()->getShippingCityName();

      $choices[$shippingMethod->getName($cityName)] = $shippingMethod->getUid();
    }

    return $choices;
  }

  protected function configureRoutes(RouteCollection $collection)
  {
    $collection->add('checkout', $this->getRouterIdParameter() . '/checkout');
    $collection->add('orderItems', $this->getRouterIdParameter() . '/store-order-orderitem/list');
    $collection->add('status', $this->getRouterIdParameter() . '/status');
    $collection->add('cancel', $this->getRouterIdParameter() . '/cancel');
    $collection->add('orderStatusHistory', $this->getRouterIdParameter() . '/orderStatusHistory');
  }

  public function toString($object)
  {
    if ($object instanceof Order)
    {
      if ($object->getCheckoutStateId() == Order::CHECKOUT_STATE_COMPLETE)
      {
        return $object->getDocumentNumber() ?: 'Заказ без номера';
      }

      return 'Заказ #' . $object->getId();
    }

    return null;
  }

  public function configureActionButtons($action, $object = null)
  {
    $list = parent::configureActionButtons($action, $object);

    if (in_array($action, array('checkout'))
      && $this->hasAccess('list')
    )
    {
      $list['list'] = array(
        'template' => $this->getTemplate('button_list'),
      );
    }

    return $list;
  }

  protected function configureSideMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
  {
    if (!$childAdmin && !in_array($action, array('edit', 'show')))
    {
      return;
    }

    $admin = $this->isChild() ? $this->getParent() : $this;
    $id = $admin->getRequest()->get('id');

    if ($this->isGranted('LIST'))
    {
      $menu->addChild('Состав заказа', array(
        'uri' => $admin->generateUrl('main.admin.order_item.list', array('id' => $id))
      ));
    }
  }

  public function preUpdate($object)
  {
    /** @var  $object Order */
    $orderItemsFromForm = [];

    foreach ($this->getForm()->getData()->getOrderItems() as $item)
    {
      $orderItemsFromForm[] = $item->getId();
    }

    $orderItemsFromDb = $this->em->getRepository(OrderItem::class)->findBy(['order' => $object]);
    $orderItemsFromDbIds = [];

    foreach ($orderItemsFromDb as $orderItem)
    {
      $orderItemsFromDbIds[] = $orderItem->getId();
    }

    $diffArrays = array_diff($orderItemsFromDbIds, $orderItemsFromForm);

    if (count($diffArrays) > 0)
    {
      foreach ($diffArrays as $id)
      {
        $itemToRemove = $this->em->getRepository(OrderItem::class)->findOneBy(['order' => $object, 'id' => $id]);
        $this->em->remove($itemToRemove);
        $this->em->flush();
      }
    }

  }

  public function configureDatagridFilters(DatagridMapper $mapper)
  {
    $mapper
      ->add('status', 'doctrine_orm_callback', array(
        'callback' => array($this, 'getStatusTypeFilter'),
        'field_type' => 'checkbox',
        'label' => 'Показывать только заказы в работе'
      ))
      ->add('user', null, ['label' => 'Email пользователя'])
      ->add('checkoutStateId', 'doctrine_orm_string', [], 'choice', [
        'choices' => \array_flip(Order::getCheckoutStateNames())
      ]);
  }

  public function getStatusTypeFilter(ProxyQuery $queryBuilder, $alias, $field, $value)
  {
    if (!$value['value'])
    {
      return false;
    }

    $needleStatuses = $this->em->getRepository(OrderStatus::class)->getStatusChoices(true);

    if (!is_array($needleStatuses) || empty($needleStatuses))
    {
      return false;
    }

    $queryBuilder->andWhere($alias.'.orderStatus IN (' . implode(", ", $needleStatuses) . ')');

    return true;
  }

  public function getExportFields()
  {
    return array_merge(
      parent::getExportFields(),
      array(
        'orderStatus',
      ));
  }

}