<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Controller;

use StoreBundle\Entity\User\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomepageController extends Controller
{
  /**
   * Контроллер главной страницы
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function indexAction()
  {
    $banners = $this->getDoctrine()->getRepository('StoreBundle:Common\HomeBanner')
      ->findBy(['enabled' => true], ['position' => 'ASC']);

    $bestOffers = $this->getDoctrine()->getRepository('StoreBundle:Store\Catalog\Product\Product')
      ->findBestOffers();

    $viewedProducts = [];

    if ($this->getUser() instanceof User)
    {
      $viewedProducts = $this->getUser()->getViewedProductList()->getProducts(6);
    }
    
    $cheeseStories = $this
      ->getDoctrine()
      ->getRepository('StoreBundle:Text\CheeseStory')
      ->findBy(['published' => true],['position' => 'ASC']);
    
    $partners = $this->getDoctrine()->getRepository('StoreBundle:Text\Partner')->findBy([],['position' => 'ASC']);
    //Промо-баннеры с преимуществами сыроварни
    $promoBanners = $this->getDoctrine()->getRepository('StoreBundle:Text\PromoBanner')
      ->findBy(['published' => true], ['position' => 'ASC']);

    return $this->render('StoreBundle:Homepage:index.html.twig', array(
      'banners' => $banners,
      'bestOffers' => $bestOffers,
      'promoBanners' => $promoBanners,
      'viewedProducts' => $viewedProducts,
      'cheeseStories' => $cheeseStories,
      'partners' => $partners
    ));
  }

}