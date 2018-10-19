<?php

namespace StoreBundle\Controller\Profile;

use StoreBundle\Entity\Notification\Notification;
use StoreBundle\Entity\Store\Logistics\Delivery\Cdek\CdekCity;
use StoreBundle\Entity\User\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\User\UserInterface;

class ProfileController extends Controller
{
  /*
   * Выключаем плашку в личном кабинете с надписью "Теперь вы можете покупать..."
   */
//  public function disableShowClubPricePopupAction(Request $request)
//  {
//    /** @var User $user */
//    $user = $this->getUser();
//    $user->setShowClubMessage(false);
//    $this->getDoctrine()->getManager()->persist($user);
//    $this->getDoctrine()->getManager()->flush();
//
//
//
//    return new Response();
//  }

  public function showAction(Request $request)
  {
    $filterForm = $this->createForm('StoreBundle\Form\Order\OrderFilterType', null, [
      'csrf_protection' => !$request->isXmlHttpRequest(),
    ]);

    $filterForm->handleRequest($request);
    $query = $this->getDoctrine()->getRepository('StoreBundle:Store\Order\Order')
      ->getUserCompleteOrdersQueryBuilder($this->getUser());

    if ($request->isXmlHttpRequest())
    {
      $data = json_decode($request->getContent(), true);
      $filterForm->submit($data);
    }

    if ($filterForm->isSubmitted())
    {
      if ($filterForm->isValid())
      {
        $data = $filterForm->getData();

        if ($data['date'] && isset($data['date']['dateFrom'])  && isset($data['date']['dateTo']))
        {
          $dateFrom = $data['date']['dateFrom'];
          $dateTo = $data['date']['dateTo'];
          $query
            ->andWhere('o.checkoutAt >= :dateFrom')
            ->andWhere('o.checkoutAt <= :dateTo')
            ->setParameter('dateFrom', $dateFrom)
            ->setParameter('dateTo', $dateTo);
        }

        if ($data['city'] && $data['city'] instanceof CdekCity)
        {
          $query
            ->andWhere('o.shippingCityName = :cityName')
            ->setParameter('cityName', $data['city']->getName());
        }

        if ($data['mtr'])
        {

        }
      }
      elseif ($request->isXmlHttpRequest())
      {
        return new JsonResponse([
          'errors' => $this->get('aw.client_application.transformer')->getClientModelData($filterForm, 'form.error')
        ], 400);
      }
    }

    $orders = $query->getQuery()->getResult();

    if ($request->isXmlHttpRequest())
    {
      return new JsonResponse($this->get('aw.client_application.transformer')->getClientModelCollectionData($orders, 'order'));
    }

    return $this->render('@Store/Profile/show.html.twig', array(
      'orders' => $orders,
      'filterForm' => $filterForm->createView(),
    ));
  }

  /**
   * Show the notice.
   */
  public function noticeAction()
  {
    $notices = $this->getDoctrine()
      ->getRepository('StoreBundle:Notification\Notification')
      ->findNotificationsByUser($this->getUser());
    return $this->render('@Store/Profile/notice.html.twig', [
      'notifications' => $notices,
    ]);
  }
}