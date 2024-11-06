<?php

namespace StoreBundle\Controller\Text;

use StoreBundle\Entity\Text\Dialog\Dialog;
use StoreBundle\Entity\Text\Dialog\DialogMessage;
use StoreBundle\Entity\User\User;
use StoreBundle\Event\CustomerQuestionEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CustomerQuestionController extends Controller
{
  public function indexAction (Request $request)
  {
    $user = $this->getUser();
    $user = ($user instanceof User)?$user:null;
    $question = new DialogMessage();
    $question->setUser($user);
    $question->setUserEmail($user?$user->getEmail():null);
    $question->setUserName($user?$user->getFio():null);
    $hasUser = !is_null($user);

    $form = $this->createForm('StoreBundle\Form\CustomerQuestionType', $question, [
      'csrf_protection' => !$request->isXmlHttpRequest()
    ]);

    if ($hasUser)
    {
      $form->remove('userName');
      $form->remove('userEmail');
    }

    $form->handleRequest($request);

    if ($form->isSubmitted())
    {
      if ($form->isValid())
      {
        /** @var DialogMessage $customerQuestion */
        $customerQuestion = $form->getData();

        $dialog = new Dialog();
        $dialog->addMessage($customerQuestion);
        $dialog->setDialogType(Dialog::DIALOG_TYPE_QUESTION);
        $dialog->setCreator($this->getUser());

        $this->getDoctrine()->getManager()->persist($dialog);
        $this->getDoctrine()->getManager()->flush();
        $request->getSession()->set('last.question.id', $customerQuestion->getId());
        $this->get('event_dispatcher')->dispatch('customer_question.create', new CustomerQuestionEvent($customerQuestion));

        if ($request->isXmlHttpRequest())
        {
          return new JsonResponse();
        }

        return $this->redirectToRoute('customer_question_success');
      }

      if ($request->isXmlHttpRequest())
      {
        return new JsonResponse([
          'errors' => $this->get('aw.client_application.transformer')->getClientModelData($form, 'form.error')
        ], 400);
      }
    }

    return $this->render('@Store/CustomerQuestion/index.html.twig', [
      'form' => $form->createView(),
    ]);
  }

  public function successAction(Request $request)
  {
    $lastQuestionId = $request->getSession()->get('last.question.id');
    $lastQuestion = $this->getDoctrine()->getRepository('StoreBundle:Text\Dialog\DialogMessage')->find($lastQuestionId);

    if (!$lastQuestion || ($lastQuestion->getCreatedAt() < new \DateTime('-10 min')))
    {
      throw $this->createNotFoundException();
    }

    return $this->render('@Store/CustomerQuestion/success.html.twig');
  }
}