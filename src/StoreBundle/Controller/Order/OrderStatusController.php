<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 08.02.18
 * Time: 17:05
 */

namespace StoreBundle\Controller\Order;


use StoreBundle\Entity\Store\Order\Status\OrderStatusReason;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class OrderStatusController extends Controller
{

  public function cancelStatusTemplateAction(Request $request)
  {
    $reasons = $this->getDoctrine()->getRepository(OrderStatusReason::class)->findAll();

    $templates = $this->toJson($reasons);

    return new JsonResponse($templates);
  }

  protected function toJson(array $data)
  {
    $json = [];

    foreach ($data as $datum)
    {
      $json[] =
        [
          'title' => $datum->getName(),
          'description' => 'Примечание для отмены товара.',
          'content' => $datum->getText()
        ];
    }
    return $json;
  }

}