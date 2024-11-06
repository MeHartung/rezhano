<?php
/**
 * (c) 2017 ИП Рагозин Денис Николаевич. Все права защищены.
 *
 * Настоящий файл является частью программного продукта, разработанного ИП Рагозиным Денисом Николаевичем
 * (ОГРНИП 315668300000095, ИНН 660902635476).
 *
 * Алгоритм и исходные коды программного кода программного продукта являются коммерческой тайной
 * ИП Рагозина Денис Николаевича. Любое их использование без согласия ИП Рагозина Денис Николаевича рассматривается,
 * как нарушение его авторских прав.
 *
 * Ответственность за нарушение авторских прав наступает в соответствии с действующим законодательством РФ.
 */

/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 26.10.2017
 * Time: 21:16
 */

namespace StoreBundle\Controller\Admin\Store\Order;

use StoreBundle\Entity\Store\Order\Order;
use StoreBundle\Entity\Store\Order\Status\OrderStatusHistory;
use StoreBundle\Entity\Store\Order\Status\OrderStatus;
use StoreBundle\Form\Admin\Checkout\CheckoutAdminType;
use StoreBundle\Form\Admin\OrderStatusHistoryType;
use StoreBundle\Form\Admin\Status\StatusAdminType;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;

class OrderAdminController extends Controller
{
  public function checkoutAction(Request $request)
  {
    $id = $request->get($this->admin->getIdParameter());

    /**
     * @var $existingObject Order
     */
    $existingObject = $this->admin->getObject($id);

    if (!$existingObject)
    {
      throw $this->createNotFoundException(sprintf('unable to find the object with id : %s', $id));
    }

    $this->admin->setSubject($existingObject);

    $form = $this->createForm(CheckoutAdminType::class, $existingObject);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid())
    {
      $order = $form->getData();

      $this->get('store.checkout.processor')->process($order, array(
        'preserve_calculations' => true,
        'isAdminEdit' => true
      ));

      $this->addFlash(
        'sonata_flash_success',
        $this->trans(
          'order_checkout_success',
          array('%documentNumber%' => $this->escapeHtml($order->getDocumentNumber())),
          'SonataAdminBundle'
        )
      );

      return new RedirectResponse($this->admin->generateUrl('list', array('filter' => $this->admin->getFilterParameters())));
    }

    $formView = $form->createView();
    // set the theme for the current Admin Form
    $this->setFormTheme($formView, $this->admin->getFormTheme());

    return $this->render('@Store/CRUD/Store/Order/checkout.html.twig', [
      'action' => 'checkout',
      'object' => $existingObject,
      'form' => $form->createView()
    ]);
  }

  /**
   * Sets the admin form theme to form view. Used for compatibility between Symfony versions.
   *
   * @param FormView $formView
   * @param string $theme
   */
  private function setFormTheme(FormView $formView, $theme)
  {
    $twig = $this->get('twig');

    try
    {
      $twig
        ->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')
        ->setTheme($formView, $theme);
    } catch (\Twig_Error_Runtime $e)
    {
      // BC for Symfony < 3.2 where this runtime not exists
      $twig
        ->getExtension('Symfony\Bridge\Twig\Extension\FormExtension')
        ->renderer
        ->setTheme($formView, $theme);
    }
  }

  /**
   * @param Request $request
   * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
   * @throws \Exception
   */
  public function statusAction(Request $request)
  {
    /** @var Order $object */
    $object = $this->admin->getSubject();
    $existingObject = $this->admin->getObject($object->getId());

    list($statusId, $reason) = $this->getFormDefaultData($object);

    $formDedaultData = [
      'active' => true,
      'statusId' => $statusId,
      'reason' => $reason
    ];

    $form = $this->createForm(StatusAdminType::class, $formDedaultData);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid())
    {
      $data = $form->getData();

      $em = $this->getDoctrine()->getManager();

      $status = $em->getRepository(OrderStatus::class)->findOneBy(['id' => $data['status']]);
      $order = $em->getRepository(Order::class)->findOneBy(['id' => $object->getId()]);

      try
      {
        $this->get('store.order.order_status.service')->setOrderStatus($data, $order, $status, $this->getUser());
      } catch (\Exception $exception)
      {
        $this->addFlash('sonata_flash_error', $exception->getCode() . ' ' . $exception->getMessage());
        $form->addError(new FormError($exception->getMessage()));

        return $this->render('@Store/CRUD/Store/Order/status.html.twig', [
          'errors' => $form->getErrors(),
          'action' => 'status',
          'object' => $existingObject,
          'form' => $form->createView()
        ]);
      }

      if ($data['notification'] == true)
      {
        $this->get('store.service.send_email_notification')->sendEmail($object, $status->getNotificationTemplate()->getId());
      }

      return new RedirectResponse($this->admin->generateUrl('list', array('filter' => $this->admin->getFilterParameters())));
    }

    foreach ($form as $field)
    {
      foreach ($field->getErrors(true) as $error)
      {
        $this->addFlash('sonata_flash_error', $this->trans(ucfirst($field->getName()), [], 'messages') . ": " . $error->getMessage());
      }
    }

    return $this->render('@Store/CRUD/Store/Order/status.html.twig', [
      'errors' => $form->getErrors(),
      'action' => 'status',
      'object' => $existingObject,
      'form' => $form->createView()
    ]);
  }

  /**
   * @param Request $request
   * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
   */
  public function cancelAction(Request $request)
  {
    /** @var Order $object */
    $object = $this->admin->getSubject();
    $existingObject = $this->admin->getObject($object->getId());

    list($statusId, $reason) = $this->getFormDefaultData($object);

    $formDedaultData = [
      'active' => false,
      'statusId' => $statusId,
      'reason' => $reason
    ];

    $form = $this->createForm(StatusAdminType::class, $formDedaultData);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid())
    {
      $data = $form->getData();

      $em = $this->getDoctrine()->getManager();

      $status = $em->getRepository(OrderStatus::class)->findOneBy(['id' => $data['status']]);
      $order = $em->getRepository(Order::class)->findOneBy(['id' => $object->getId()]);

      try
      {
        $this->get('store.order.order_status.service')->setOrderStatus($data, $order, $status, $this->getUser());
      } catch (\Exception $exception)
      {
        $this->addFlash('sonata_flash_error', $exception->getCode() . ' ' . $exception->getMessage());

        $form->addError(new FormError($exception->getMessage()));

        return $this->render('@Store/CRUD/Store/Order/status.html.twig', [
          'errors' => $form->getErrors(),
          'action' => 'status',
          'object' => $existingObject,
          'form' => $form->createView()
        ]);
      }

      if ($data['notification'] == true)
      {
        $this->get('store.service.send_email_notification')->sendEmail($object, $status->getNotificationTemplate()->getId());
      }

      return new RedirectResponse($this->admin->generateUrl('list', array('filter' => $this->admin->getFilterParameters())));
    }

    foreach ($form as $field)
    {
      foreach ($field->getErrors(true) as $error)
      {
        $this->addFlash('sonata_flash_error', $this->trans(ucfirst($field->getName()), [], 'messages') . ": " . $error->getMessage());
      }
    }

    return $this->render('@Store/CRUD/Store/Order/status.html.twig', [
      'errors' => $form->getErrors(),
      'action' => 'status',
      'object' => $existingObject,
      'form' => $form->createView()
    ]);
  }

  public function orderStatusHistoryAction(Request $request)
  {
    $order = $this->admin->getSubject();

    if(!$order)
    {
      throw new MissingMandatoryParametersException("Missing mandatory parameter order");
    }

    $orderHistory = $this->getDoctrine()->getRepository(OrderStatusHistory::class)->findBy(['order' => $order], ["createdAt" => "DESC"]);
    $form = $this->createForm(OrderStatusHistoryType::class);

    return $this->render("@Store/Admin/Store/Order/order_status_history.html.twig", [
      'orderStatusHistory' => $orderHistory,
      'form' => $form->createView(),
      "object" => $order,
      "action" => "preview"
    ]);
  }

  private function getFormDefaultData(Order $order)
  {
    $orderHasStatus = (boolean)$order->getOrderStatus();

    if (!$orderHasStatus)
    {
      return [null, null];
    }

    return [$order->getOrderStatus()->getId(), $order->getOrderStatusHistory()->last()->getReason()];
  }

}