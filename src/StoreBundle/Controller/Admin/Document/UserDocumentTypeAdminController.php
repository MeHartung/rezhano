<?php

namespace StoreBundle\Controller\Admin\Document;


use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class UserDocumentTypeAdminController extends CRUDController
{
  public function moveAction (Request $request, $position)
  {
    $translator = $this->get('translator');
    $field = $request->get('field');

    if (!$this->admin->isGranted('EDIT'))
    {
      $this->addFlash(
        'sonata_flash_error',
        $translator->trans('flash_error_no_rights_update_position')
      );

      return new RedirectResponse($this->admin->generateUrl(
        'list',
        array('filter' => $this->admin->getFilterParameters())
      ));
    }

    if (!$field)
    {
      return new JsonResponse([], 400);
    }

    $object = $this->admin->getSubject();
    $lastPositionNumber = $this->admin->getLastPosition($object);
    $newPositionNumber = $this->getPosition($object, $position, $lastPositionNumber);

    $setter = null;

    switch ($field)
    {
      case 'positionIndividual':
        $setter = 'setPositionIndividual';
        break;
      case 'positionJuridical':
        $setter = 'setPositionJuridical';
        break;
      case 'positionEnterpreneur':
        $setter = 'setPositionEnterpreneur';
        break;
    }

    $object->{$setter}($newPositionNumber);
    $this->admin->update($object);

    if ($this->isXmlHttpRequest())
    {
      return $this->renderJson(array(
        'result' => 'ok',
        'objectId' => $this->admin->getNormalizedIdentifier($object)
      ));
    }

    $this->addFlash(
      'sonata_flash_success',
      $translator->trans('flash_success_position_updated')
    );

    return new RedirectResponse($this->admin->generateUrl(
      'list',
      array('filter' => $this->admin->getFilterParameters())
    ));
  }

  private function getPosition ($object, $movePosition, $lastPosition)
  {
    $currentPosition = $this->admin->getCurrentObjectPosition($object);
    $newPosition = 0;

    switch ($movePosition)
    {
      case 'up' :
        if ($currentPosition > 0)
        {
          $newPosition = $currentPosition - 1;
        }
        break;

      case 'down':
        if ($currentPosition < $lastPosition)
        {
          $newPosition = $currentPosition + 1;
        }
        break;

      case 'top':
        if ($currentPosition > 0)
        {
          $newPosition = 0;
        }
        break;

      case 'bottom':
        if ($currentPosition < $lastPosition)
        {
          $newPosition = $lastPosition;
        }
        break;

      default:
        if (is_numeric($movePosition))
        {
          $newPosition = (int)$movePosition;
        }

    }

    return $newPosition;
  }
}