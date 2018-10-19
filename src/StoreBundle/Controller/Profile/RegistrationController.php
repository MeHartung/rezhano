<?php

namespace StoreBundle\Controller\Profile;

use AccurateCommerce\Util\UUID;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserManagerInterface;
use StoreBundle\Entity\Document\UserDocument;
use StoreBundle\Entity\User\User;
use StoreBundle\Form\DataTransformer\Base64Transformer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class RegistrationController extends Controller
{
  public function registerAction (Request $request)
  {
    /** @var $userManager UserManagerInterface */
    $userManager = $this->get('fos_user.user_manager');
    /** @var $dispatcher EventDispatcherInterface */
    $dispatcher = $this->get('event_dispatcher');

    $user = $userManager->createUser();
    $user->setEnabled(true);

    $event = new GetResponseUserEvent($user, $request);
    $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

    if (null !== $event->getResponse())
    {
      return $event->getResponse();
    }

    $juridicalForm = $this->createForm('StoreBundle\Form\User\JuridicalRegisterType', $user, [
      'csrf_protection' => !$request->isXmlHttpRequest(),
    ]);
    $individualForm = $this->createForm('StoreBundle\Form\User\IndividualRegisterType', $user, [
      'csrf_protection' => !$request->isXmlHttpRequest(),
    ]);
    $enterpreneurForm = $this->createForm('StoreBundle\Form\User\EnterpreneurRegisterType', $user, [
      'csrf_protection' => !$request->isXmlHttpRequest(),
    ]);

    if ($request->isXmlHttpRequest())
    {
      $data = @\json_decode($request->getContent(), true);

      if ($data && isset($data['roles']))
      {
        $uploadedFiles = $request->getSession()->get('registration.file.uploads');

        if (is_array($uploadedFiles))
        {
          $documents = [];

          foreach ($uploadedFiles as $uploadedFile)
          {
            $file = $this->getDoctrine()->getRepository('StoreBundle:Document\UserDocument')->findOneBy(['uuid' => $uploadedFile]);

            if ($file)
            {
              try
              {
                $documents[] = [
                  'file' => $this->get('store.document.storage')->getFile($file),
                  'name' => $file->getName(),
                  'documentType' => $file->getDocumentType()->getId(),
                ];
              }
              catch (FileNotFoundException $e)
              {
                $this->get('logger')->addError(sprintf('Registration fail: File %s not found', $file->getFile()));
              }
            }
          }

          $data['documents'] = $documents;
        }

        switch (reset($data['roles']))
        {
          case User::ROLE_JURIDICAL:
            $juridicalForm->submit($data);
            break;
          case User::ROLE_INDIVIDUAL:
            $individualForm->submit($data);
            break;
          case User::ROLE_ENTREPRENEUR:
            $enterpreneurForm->submit($data);
            break;
          default:
            return new JsonResponse(['errors' => ["_global" => sprintf('Неизвестный тип пользователя "%s"', reset($data['roles']))]], 400);
            break;
        }
      }
      else
      {
        return new JsonResponse(['errors' => ["_global" => 'Не указан тип пользователя']], 400);
      }
    }
    else
    {
      $juridicalForm->handleRequest($request);
      $individualForm->handleRequest($request);
      $enterpreneurForm->handleRequest($request);
    }

    if ($juridicalForm->isSubmitted())
    {
      $form = $juridicalForm;
    }
    elseif ($individualForm->isSubmitted())
    {
      $form = $individualForm;
    }
    elseif ($enterpreneurForm->isSubmitted())
    {
      $form = $enterpreneurForm;
    }

    if (isset($form) && $form->isSubmitted())
    {
      if ($form->isValid())
      {
        $event = new FormEvent($form, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);
        $userManager->updateUser($user);

        if (null === $response = $event->getResponse())
        {
          $url = $this->generateUrl('fos_user_registration_confirmed');
          $response = new RedirectResponse($url);
        }

        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

        return $response;
      }

      $event = new FormEvent($form, $request);
      $dispatcher->dispatch(FOSUserEvents::REGISTRATION_FAILURE, $event);

      if (null !== $response = $event->getResponse())
      {
        return $response;
      }
    }

    if ($request->isXmlHttpRequest())
    {
      throw $this->createNotFoundException();
    }

    $documents = $this->getDoctrine()->getRepository('StoreBundle:Document\RegistrationDocument')
      ->findBy(['show' => true]);

    $juridicalDocumentTypes = $this->getDoctrine()->getRepository('StoreBundle:Document\UserDocumentType')->findForJuridical();
    $individualDocumentTypes = $this->getDoctrine()->getRepository('StoreBundle:Document\UserDocumentType')->findForIndividual();
    $enterpreneurDocumentTypes = $this->getDoctrine()->getRepository('StoreBundle:Document\UserDocumentType')->findForEnterpreneur();

    return $this->render('@Store/Profile/register.html.twig', array(
      'juridicalForm' => $juridicalForm->createView(),
      'individualForm' => $individualForm->createView(),
      'enterpreneurForm' => $enterpreneurForm->createView(),
      'documents' => $documents,
      'juridicalDocumentTypes' => $juridicalDocumentTypes,
      'individualDocumentTypes' => $individualDocumentTypes,
      'enterpreneurDocumentTypes' => $enterpreneurDocumentTypes,
    ));
  }

  public function uploadRegisterDocumentAction (Request $request, $typeId)
  {
    $documentType = $this->getDoctrine()->getRepository('StoreBundle:Document\UserDocumentType')->find($typeId);

    if (!$documentType)
    {
      throw $this->createNotFoundException(sprintf('Type %s not found', $typeId));
    }

    $form = $this->createForm('StoreBundle\Form\Document\UserDocumentType', null, [
      'documentType' => $documentType,
      'documentName' => $documentType->getName(),
      'csrf_protection' => false,
    ]);

    $data = json_decode($request->getContent(), true);
    $file = $data['file'];

    $transformer = new Base64Transformer();
    $file = $transformer->reverseTransform($file);

    $form->submit([
      'file' => $file,
      'name' => $documentType->getName(),
      'documentType' => $documentType->getId(),
    ]);


    if ($form->isSubmitted() && $form->isValid())
    {
      /** @var UserDocument $document */
      $document = $form->getData();
      $uid = (string)UUID::mint(4);
      $document->setUuid($uid);
      $this->getDoctrine()->getManager()->persist($document);
      $this->getDoctrine()->getManager()->flush();
      $uploadedFiles = $request->getSession()->get('registration.file.uploads');

      if (!is_array($uploadedFiles))
      {
        $uploadedFiles = [];
      }

      $uploadedFiles[] = $uid;
      $request->getSession()->set('registration.file.uploads', $uploadedFiles);

      return new JsonResponse([
        'fileUID' => $uid,
      ], 200);
    }

    return new JsonResponse([
      'errors' => $this->get('aw.client_application.transformer')->getClientModelData($form, 'form.error')
    ], 400);
  }

  public function successAction(Request $request)
  {
    return $this->render('@Store/Profile/register_success.html.twig');
  }
}