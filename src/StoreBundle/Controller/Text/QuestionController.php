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
    
    $form = $this->createForm(UserQuestionType::class, $question);
    
    $form->handleRequest($request);
    
    if ($form->isSubmitted() && $form->isValid())
    {
      $questionData = $form->getData();
      
      $em = $this->getDoctrine()->getManager();
      
      $em->persist($questionData);
      $em->flush();
      $request->getSession()->set('last.question.id', $questionData->getId());
      $operatorEmail = $this->getParameter('operator_email');
      if ($operatorEmail)
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
      
      return $this->redirectToRoute('customer_question_success');
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