<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 07.08.17
 * Time: 12:06
 */

namespace StoreBundle\Controller\Text;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SpecialOfferController extends Controller
{
  public function indexAction()
  {
    $now = new \DateTime();

    $query = $this->getDoctrine()->getManager()
      ->createQuery(
        'SELECT s
       FROM StoreBundle:Text\SpecialOffer s
       WHERE s.dateEnd > :now AND s.dateStart <= :now
       ORDER BY s.dateStart DESC'
      )
      ->setParameter('now', $now);

    $specialOffers = $query->getResult();

    $expiredSpecialOffers = $this->getDoctrine()->getManager()
      ->createQuery(
        'SELECT s
       FROM StoreBundle:Text\SpecialOffer s
       WHERE s.dateEnd < :now 
       ORDER BY s.dateEnd'
      )
      ->setParameter('now', $now)
      ->getResult();

    return $this->render('StoreBundle:SpecialOffer:index.html.twig', [
      'specialOffers' => $specialOffers,
      'expiredSpecialOffers' => $expiredSpecialOffers
    ]);

  }

  public function showAction($slug)
  {
    $specialOffers = $this->getDoctrine()
      ->getRepository('StoreBundle:Text\SpecialOffer')
      ->findOneBy(['slug'=>$slug]);

    if (!$specialOffers)
    {
      throw $this->createNotFoundException('Акция %s не найдена. Возможно, она закончилась.', $slug);
    }

    return $this->render('StoreBundle:SpecialOffer:show.html.twig', [
      'specialOffers' => $specialOffers,
    ]);
  }

  public function _sidebarAction()
  {
    $specialOffers = $this->getDoctrine()
      ->getRepository('StoreBundle:Text\SpecialOffer')
      ->findRecent(3);

    $now = new \DateTime();
    $specialOffers = $this->getDoctrine()->getManager()
      ->createQuery(
        'SELECT s
       FROM StoreBundle:Text\SpecialOffer s
       WHERE s.dateEnd > :now AND s.dateStart <= :now
       ORDER BY s.dateStart DESC'
      )
      ->setParameter('now', $now)
      ->setMaxResults(3)
      ->getResult();

    return $this->render('StoreBundle:SpecialOffer:sidebar.html.twig', array(
      'special_offers' => $specialOffers
    ));
  }
}