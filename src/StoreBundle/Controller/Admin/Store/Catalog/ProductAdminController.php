<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 16.01.18
 * Time: 11:54
 */

namespace StoreBundle\Controller\Admin\Store\Catalog;


use StoreBundle\Entity\Store\Catalog\Product\Product;
use PHPUnit\Runner\Exception;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductAdminController extends Controller
{
  public function cloneAction($id)
  {
    $object = $this->admin->getSubject();

    if (!$object) {
      throw new NotFoundHttpException(sprintf('unable to find the object with id: %s', $id));
    }

    /** @var  $clonedObject Product */
    $clonedObject = clone $object;

    try
    {
      $this->admin->create($clonedObject);

      $this->addFlash('sonata_flash_success', $object->getName().' успешно скопирован.');
    }
    catch (\Exception $e)
    {
      $this->addFlash('sonata flash_error', $e->getCode() . $e->getMessage());
      return new RedirectResponse($this->admin->generateUrl('list'));
    }

    /*['filter' => $this->admin->getFilterParameters()]*/
    return new RedirectResponse($this->admin->generateUrl('edit',  ['id' => $clonedObject->getId()]));

  }
}