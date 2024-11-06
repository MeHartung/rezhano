<?php

namespace StoreBundle\Controller\Profile;

use StoreBundle\Entity\Text\Dialog\Dialog;
use StoreBundle\Entity\Text\Dialog\DialogMessage;
use StoreBundle\Event\CustomerQuestionEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class DialogController extends Controller
{
  public function messagesListAction(Request $request, $dialogId)
  {
    $dialog = $this->getDoctrine()->getRepository('StoreBundle:Text\Dialog\Dialog')->find($dialogId);
    $user = $this->getUser();

    if (!$dialog)
    {
      throw $this->createNotFoundException(sprintf('Dialog %s not found', $dialogId));
    }

    if ($dialog->getCreator()->getId() !== $user->getId())
    {
      /*
       * Не из админки общаться в диалоге может только тот, кто его создал (задал вопрос)
       */
      throw new AccessDeniedHttpException();
    }

    $messages = $dialog->getMessages();

    return new JsonResponse($this->get('aw.client_application.transformer')->getClientModelCollectionData($messages, 'dialog.message'));
  }

  public function addMessageAction(Request $request, $dialogId)
  {
    $dialog = $this->getDoctrine()->getRepository('StoreBundle:Text\Dialog\Dialog')->find($dialogId);
    $user = $this->getUser();

    if (!$dialog)
    {
      throw $this->createNotFoundException(sprintf('Dialog %s not found', $dialogId));
    }

    if ($dialog->getCreator()->getId() !== $user->getId())
    {
      /*
       * Не из админки общаться в диалоге может только тот, кто его создал (задал вопрос)
       */
      throw new AccessDeniedHttpException();
    }

    $message = new DialogMessage();
    $message
      ->setUser($user)
      ->setDialog($dialog)
      ->setUserName($user->getFio())
      ->setUserEmail($user->getEmail());
    $form = $this->createForm('StoreBundle\Form\User\Dialog\DialogMessageType', $message, [
      'csrf_protection' => false,
    ]);
    $data = json_decode($request->getContent(), true);
    $form->submit($data);

    if ($form->isSubmitted() && $form->isValid())
    {
      $message = $form->getData();
      $this->getDoctrine()->getManager()->persist($message);
      $this->getDoctrine()->getManager()->flush();

      if ($dialog->getDialogType() === Dialog::DIALOG_TYPE_QUESTION)
      {
        $this->get('event_dispatcher')->dispatch('customer_question.message', new CustomerQuestionEvent($message));
      }

      return new JsonResponse($this->get('aw.client_application.transformer')->getClientModelData($message, 'dialog.message'), 200);
    }

    return new JsonResponse([
      'errors' => $this->get('aw.client_application.transformer')->getClientModelData($form, 'form.error')
    ], 400);
  }
}