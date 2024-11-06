<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace Accurateweb\MediaBundle\Controller;

use Accurateweb\MediaBundle\Generator\ImageThumbnailGenerator;
use Accurateweb\MediaBundle\Model\Media\MediaInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Exception\NotImplementedException;

class MediaController extends Controller
{
  /**
   * @param Request $request
   * @throws \Exception
   */
  public function uploadAction(Request $request)
  {
    $gallery = $this->get('aw.media.manager')->getGallery($request->get('gallery_provider_id'), $request->get('gallery_id'));

    $mediaObjectManager = $gallery->getMediaObjectManager();

    $result = [];
    $statusCode = 200;

    $files = $request->files->all();
    foreach ($files as $id => $file)
    {

      /** @var $file UploadedFile */
      $media = $gallery->createMedia();

      $resourceId = implode('/', [
        $request->get('gallery_provider_id'),
        $request->get('gallery_id'),
        md5(uniqid()).($file->guessExtension() ? '.'.$file->guessExtension() : '')
      ]);

      $media->setResourceId($resourceId);

      $mediaObjectManager->persist($media);
      $mediaObjectManager->flush();

      $storage = $this->get('aw.media.storage.provider')->getMediaStorage($media);

      $storage->store($media, $file);

      $this->get('aw_media.thumbnail_generator')->generate($media);

      $result[$id] = $this->mediaToArray($media);
    }

    return new JsonResponse($result, $statusCode);
  }

  public function listAction(Request $request)
  {
    $gallery = $this->get('aw.media.manager')->getGallery($request->get('gallery_provider_id'), $request->get('gallery_id'));

    $medias = $gallery->getMediaObjectManager()->getRepository()->getAll();

    $result = [];

    foreach ($medias as $media)
    {
      $result[] = $this->mediaToArray($media);
    }

    return new JsonResponse($result);
  }

  protected function mediaToArray(MediaInterface $media)
  {
    return [
      'id' => $media->getId(),
      'public_url' => '/uploads/'.$media->getResourceId()
    ];
  }
}