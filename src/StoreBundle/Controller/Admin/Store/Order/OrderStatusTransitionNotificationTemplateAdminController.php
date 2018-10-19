<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 04.04.18
 * Time: 22:57
 */

namespace StoreBundle\Controller\Admin\Store\Order;


use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Request;

class OrderStatusTransitionNotificationTemplateAdminController extends CRUDController
{

public function preCreate(Request $request, $object)
{
  if($name = $request->get('status_name'))
  {
    $object->setTitle($name);
  }
}

}