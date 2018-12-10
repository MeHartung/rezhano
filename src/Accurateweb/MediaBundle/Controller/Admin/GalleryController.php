<?php

namespace Accurateweb\MediaBundle\Controller\Admin;

use Accurateweb\MediaBundle\Generator\ImageThumbnailGenerator;
use Accurateweb\MediaBundle\Model\Media\ImageInterface;
use Accurateweb\MediaBundle\Model\Media\MediaCroppableInterface;
use Accurateweb\MediaBundle\Model\Thumbnail\ImageThumbnail;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */
class GalleryController extends Controller
{
  public function listAction(Request $request)
  {
    $gallery = $this->get('aw.media.manager')->getGallery($request->get('gallery_provider_id'), $request->get('gallery_id'));

    $medias = $gallery->getMediaObjectManager()->getRepository()->getAll();

    $result = [];

    foreach ($medias as $media)
    {
      $result[] = $this->imageToArray($media);
    }

    return new JsonResponse($result);
  }

  public function deleteAction(Request $request)
  {
    $gallery = $this->get('aw.media.manager')->getGallery($request->get('gallery_provider_id'), $request->get('gallery_id'));

    $mediaObjectManager = $gallery->getMediaObjectManager();

    $media = $mediaObjectManager->getRepository()->find($request->request->get('media_id'));

    if (!$media)
    {
      throw new NotFoundHttpException();
    }

    $storage = $this->get('aw.media.storage.provider')->getMediaStorage($media);

    $storage->remove($media);

    $mediaObjectManager->remove($media);
    $mediaObjectManager->flush();

    return new JsonResponse();
  }

  protected function imageToArray(ImageInterface $media)
  {
    $thumbnail = new ImageThumbnail('preview', $media);

    $mediaStorage = $this->get('aw.media.storage.provider')->getMediaStorage($thumbnail);

    $thumbnailResource = $mediaStorage->retrieve($thumbnail);
    $mediaResource = $mediaStorage->retrieve($media);
    $crop = array(null, null, null, null);

    return [
      'id' => $media->getId(),
      'crop' => ($media instanceof MediaCroppableInterface)?$media->getCrop():$crop,
      'preview' => array(
        'id' => $thumbnail->getId(),
        'src' => $thumbnailResource ? $thumbnailResource->getUrl() : null
      ),
      'src' => $mediaResource ? $mediaResource->getUrl() : null
    ];
  }
}