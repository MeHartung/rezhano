<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 14.11.17
 * Time: 17:36
 */

namespace StoreBundle\Controller;

use StoreBundle\Entity\Store\Catalog\Product\ProductImage;
use StoreBundle\Entity\User\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ImageController extends Controller
{
  /**
   * @param Request $request
   * @return JsonResponse|BadRequestHttpException
   */
  public function moveAction(Request $request)
  {
    $requestPosition = $request->get('position');
    $requestId = $request->get('image_id');

    try
    {
      $response = new JsonResponse($this->get('store.product.image.move')->moveImage($requestId, $requestPosition), 200);
    }
    catch (\Exception $e)
    {
      $response = new JsonResponse(['error' => $e->getMessage()], 400);
    }

    return $response;
  }

}