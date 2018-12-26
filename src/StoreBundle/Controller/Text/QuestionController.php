<?php

namespace StoreBundle\Controller\Text;


use StoreBundle\Entity\Text\Question;
use StoreBundle\Form\Text\UserQuestionType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class QuestionController extends Controller
{
  /**
   * Контроллер действия "Задать вопрос по этому товару"
   */
  public function askQuestionAction(Request $request)
  {
    $question = new Question();
    $question->setSource('question');
    $form = $this->createForm(UserQuestionType::class, $question);
    $form->handleRequest($request);

    if ($request->isXmlHttpRequest())
    {
      $data = json_decode($request->getContent(), true);
      $form->submit($data);
    }
    
    if ($form->isSubmitted())
    {
      if ($form->isValid())
      {
        $questionData = $form->getData();
        $em = $this->getDoctrine()->getManager();
        $em->persist($questionData);
        $em->flush();
        $request->getSession()->set('last.question.id', $questionData->getId());
//        $operatorEmail = $this->getParameter('operator_email');
        $operatorEmail = $this->get('aw.settings.manager')->getValue('operator_email');

        if ($operatorEmail)
        {
          try
          {
            $email = $this->get('aw_email_templating.template.factory')->createMessage(
              'user_question_operator',
              array($this->getParameter('mailer_from') => $this->getParameter('mailer_sender_name')),
              array($operatorEmail => ''),
              array(
                'customer_name' => $question->getFio(),
                'customer_email' => $question->getEmail(),
                'question' => $question->getText()
              ));

            $this->get('mailer')->send($email);
          }
          catch (\Exception $e)
          {
            $this->get('logger')->error(sprintf('Failed to send email for operator. %s', $e->getMessage()));
          }
        }

        if ($request->isXmlHttpRequest())
        {
          return new JsonResponse($this->get('aw.client_application.transformer')->getClientModelData($questionData, 'question'));
        }

        return $this->redirectToRoute('customer_question_success');
      }

      if ($request->isXmlHttpRequest())
      {
        return new JsonResponse($this->get('aw.client_application.transformer')->getClientModelData($form, 'form.error'), 400);
      }
    }
    
    return $this->render('@Store/CustomerQuestion/index.html.twig', [
      'form' => $form->createView(),
      'form_errors' => $form->getErrors(true)
    ]);
  }
  
  public function successAction(Request $request)
  {
    $lastQuestionId = $request->getSession()->get('last.question.id');
    $lastQuestion = $this->getDoctrine()->getRepository('StoreBundle:Text\Question')->find($lastQuestionId);
    
    if (!$lastQuestion || ($lastQuestion->getCreatedAt() < new \DateTime('-10 min')))
    {
      throw $this->createNotFoundException();
    }
    
    return $this->render('@Store/CustomerQuestion/success.html.twig');
  }
}