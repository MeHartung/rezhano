<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 14.11.17
 * Time: 17:36
 */

namespace StoreBundle\Controller;

use Accurateweb\ImagingBundle\Adapter\GdImageAdapter;
use Accurateweb\ImagingBundle\Filter\GD\CropFilter;
use Accurateweb\MediaBundle\Model\Media\Storage\FileMediaStorage;
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

  public function cropAction(Request $request)
  {
    $coords = $request->get('coords');
    $image_id = $request->get('image_id');

    if (!$coords || !$image_id)
    {
      throw $this->createNotFoundException(sprintf('Required parameters not exists'));
    }

    $productImage = $this->getDoctrine()->getRepository('StoreBundle:Store\Catalog\Product\ProductImage')->find($image_id);

    if (!$productImage)
    {
      throw $this->createNotFoundException('Image not found');
    }

//    $adapter = $this->get('aw_imaging.adapter.gd');
//    /** @var FileMediaStorage $storage */
//    $storage = $this->get('aw.media.manager')->getMediaStorage($productImage);
//    $fileName = $storage->getOriginalFilePath($productImage);
//    $image = $adapter->loadFromFile($fileName);
//
//    /** @var CropFilter $cropFilter */
//    $cropFilter = $this->get('aw_imaging.filter.factory.gd')
//      ->create('crop', [
//        'left' => $coords[0],
//        'top' => $coords[1],
//        'width' => $coords[2],
//        'height' => $coords[3],
//      ]);
//    $cropFilter->process($image);
//    $adapter->save($image, $fileName);
    $productImage->setCrop($coords);
    $this->getDoctrine()->getManager()->persist($productImage);
    $this->getDoctrine()->getManager()->flush();
    $this->get('aw_media.thumbnail_generator')->generate($productImage);

    return new JsonResponse();
  }
}