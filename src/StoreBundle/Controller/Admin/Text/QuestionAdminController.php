<?php

namespace StoreBundle\Controller\Admin\Text;

use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Exception\LockException;
use Sonata\AdminBundle\Exception\ModelManagerException;
use StoreBundle\Entity\Text\Question;
use StoreBundle\Event\QuestionAnswerEvent;
use StoreBundle\Form\Text\QuestionType;
use Symfony\Bridge\Twig\AppVariable;
use Symfony\Bridge\Twig\Command\DebugCommand;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Form\TwigRenderer;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Form\FormView;


class QuestionAdminController extends CRUDController
{
  public function editAction($id = null)
  {
    $request = $this->getRequest();
    // the key used to lookup the template
    $templateKey = 'edit';
    $backpath = $this->get('app.admin.user_qustion')->generateUrl('list');
    $id = $request->get($this->admin->getIdParameter());
    $existingObject = $this->admin->getObject($id);

    if (!$existingObject)
    {
      throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
    }
    
    $this->admin->checkAccess('edit', $existingObject);
    
    $preResponse = $this->preEdit($request, $existingObject);
    if (null !== $preResponse)
    {
      return $preResponse;
    }
    
    $this->admin->setSubject($existingObject);
    $objectId = $this->admin->getNormalizedIdentifier($existingObject);
    $answerAt =$existingObject->getAnswerAt();
    
      /** @var $form Form */
    $form = $this->createForm(QuestionType::class, $existingObject);
    $form->setData($existingObject);
    $form->handleRequest($request);
    
    if ($form->isSubmitted())
    {
      $isFormValid = $form->isValid();
      
      // persist if the form was valid and if in preview mode the preview was approved
      if ($isFormValid && (!$this->isInPreviewMode() || $this->isPreviewApproved()))
      {
        /** @var $submittedObject Question */
        $submittedObject = $form->getData();
        $this->admin->setSubject($submittedObject);
  
        if($submittedObject->getEmail() && $submittedObject->getAnswer())
        {
          $this->get('event_dispatcher')->dispatch('question.answer', new QuestionAnswerEvent($submittedObject));
        }
        
        $submittedObject->setAnswerAt(new \DateTime());
        
        try
        {
          $existingObject = $this->admin->update($submittedObject);
          
          if ($this->isXmlHttpRequest())
          {
            return $this->renderJson([
              'result' => 'ok',
              'objectId' => $objectId,
              'objectName' => $this->escapeHtml($this->admin->toString($existingObject)),
            ], 200, []);
          }
          
          $this->addFlash(
            'sonata_flash_success',
            $this->trans(
              'flash_edit_success',
              ['%name%' => $this->escapeHtml($this->admin->toString($existingObject))],
              'SonataAdminBundle'
            )
          );
          

          
          // redirect to edit mode
          return $this->redirectTo($existingObject);
        } catch (ModelManagerException $e)
        {
          $this->handleModelManagerException($e);
          
          $isFormValid = false;
        } catch (LockException $e)
        {
          $this->addFlash('sonata_flash_error', $this->trans('flash_lock_error', [
            '%name%' => $this->escapeHtml($this->admin->toString($existingObject)),
            '%link_start%' => '<a href="' . $this->admin->generateObjectUrl('edit', $existingObject) . '">',
            '%link_end%' => '</a>',
          ], 'SonataAdminBundle'));
        }
      }
      
      // show an error message if the form failed validation
      if (!$isFormValid)
      {
        if (!$this->isXmlHttpRequest())
        {
          $this->addFlash(
            'sonata_flash_error',
            $this->trans(
              'flash_edit_error',
              ['%name%' => $this->escapeHtml($this->admin->toString($existingObject))],
              'SonataAdminBundle'
            )
          );
        }
      } elseif ($this->isPreviewRequested())
      {
        // enable the preview template if the form was valid and preview was requested
        $templateKey = 'preview';
        $this->admin->getShow();
      }
    }
    
    $formView = $form->createView();
    // set the theme for the current Admin Form
    $this->setFormTheme($formView, $this->admin->getFormTheme());
    
    return $this->render('@Store/CRUD/Text/edit.html.twig', [
      'action' => 'edit',
      'form' => $formView,
      'object' => $existingObject,
      'objectId' => $objectId,
      'backPath' => $backpath,
      'answerAt' => $answerAt,
    ], null);/*
    return $this->renderWithExtraParams('@StoreBundle/Resources/views/CRUD/Text/edit.html.twig', [
      'action' => 'edit',
      'form' => $formView,
      'object' => $existingObject,
      'objectId' => $objectId,
    ], null);*/
  }
  
  private function setFormTheme(FormView $formView, $theme)
  {
    $twig = $this->get('twig');
    
    // BC for Symfony < 3.2 where this runtime does not exists
    if (!method_exists(AppVariable::class, 'getToken')) {
      $twig->getExtension(FormExtension::class)->renderer->setTheme($formView, $theme);
      
      return;
    }
    
    // BC for Symfony < 3.4 where runtime should be TwigRenderer
    if (!method_exists(DebugCommand::class, 'getLoaderPaths')) {
      $twig->getRuntime(TwigRenderer::class)->setTheme($formView, $theme);
      
      return;
    }
    
    $twig->getRuntime(FormRenderer::class)->setTheme($formView, $theme);
  }
}