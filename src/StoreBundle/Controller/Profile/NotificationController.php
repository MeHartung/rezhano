<?php

namespace StoreBundle\Controller\Profile;

use StoreBundle\Entity\Notification\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class NotificationController extends Controller
{
  public function listAction(Request $request)
  {
    $user = $this->getUser();

    if (!$user)
    {
      throw new AccessDeniedHttpException();
    }

    $notifications = $this->getDoctrine()
      ->getRepository('StoreBundle:Notification\Notification')
      ->findNotificationsByUser($user);

    return new JsonResponse($this->get('aw.client_application.transformer')->getClientModelCollectionData($notifications, 'notification'), 200);
  }

  public function readAction(Request $request, $id)
  {
    $user = $this->getUser();

    if (!$user)
    {
      throw new AccessDeniedHttpException();
    }

    $notification = $this->getDoctrine()->getRepository('StoreBundle:Notification\Notification')->find($id);

    if (!$notification)
    {
      throw $this->createNotFoundException(sprintf('Notification %s not found', $id));
    }

    if ($notification->getUser()->getId() !== $user->getId())
    {
      throw new AccessDeniedHttpException();
    }

    return new JsonResponse($this->get('aw.client_application.transformer')->getClientModelData($notification, 'notification'), 200);
  }

  public function updateAction(Request $request, $id)
  {
    $user = $this->getUser();

    if (!$user)
    {
      throw new AccessDeniedHttpException();
    }

    $notification = $this->getDoctrine()->getRepository('StoreBundle:Notification\Notification')->find($id);

    if (!$notification)
    {
      throw $this->createNotFoundException(sprintf('Notification %s not found', $id));
    }

    if ($notification->getUser()->getId() !== $user->getId())
    {
      throw new AccessDeniedHttpException();
    }

    $form = $this->createForm('StoreBundle\Form\Notification\NotificationType', $notification, [
      'csrf_protection' => false,
      'allow_extra_fields' => true,
    ]);
    $form->submit(json_decode($request->getContent(), true));

    if ($form->isSubmitted() && $form->isValid())
    {
      /** @var Notification $notification */
      $notification = $form->getData();

      if ($notification->isRead())
      {
        $notification->setReadAt(new \DateTime());
      }
      else
      {
        $notification->setReadAt(null);
      }

      $this->getDoctrine()->getManager()->persist($notification);
      $this->getDoctrine()->getManager()->flush();

      return new JsonResponse($this->get('aw.client_application.transformer')->getClientModelData($notification, 'notification'), 200);
    }

    return new JsonResponse([
      'errors' => $this->get('aw.client_application.transformer')->getClientModelData($form, 'form.error')
    ], 400);
  }

  public function deleteAction(Request $request, $id)
  {
    $user = $this->getUser();

    if (!$user)
    {
      throw new AccessDeniedHttpException();
    }

    $notification = $this->getDoctrine()->getRepository('StoreBundle:Notification\Notification')->find($id);

    if (!$notification)
    {
      throw $this->createNotFoundException(sprintf('Notification %s not found', $id));
    }

    if ($notification->getUser()->getId() !== $user->getId())
    {
      throw new AccessDeniedHttpException();
    }

    $this->getDoctrine()->getManager()->remove($notification);
    $this->getDoctrine()->getManager()->flush();

    return new JsonResponse([], 200);
  }
}