<?php

/*
 *  @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */

namespace StoreBundle\Controller;

use StoreBundle\Entity\Text\News;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Description of TestController
 *
 * @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
class NewsController extends Controller
{
  public function indexAction()
  {
    $em = $this->getDoctrine()->getManager();

    $query = $em->createQuery(
      'SELECT n
       FROM StoreBundle:Text\News n
       WHERE n.published > 0
       ORDER BY n.publishedAt DESC');

    $news = $query->getResult();
    //var_dump($news);die;

    return $this->render('StoreBundle:News:index.html.twig', array(
      'news' => $news
    ));
  }
  
  public function showAction($slug)
  {
    /** @var News $news */
    $news = $this
      ->getDoctrine()
      ->getRepository('StoreBundle:Text\News')
      ->findOneBy(['slug' => $slug]);

    if (!$news)
    {
      throw $this->createNotFoundException(sprintf('Новость "%s" не найдена. Возможно, она была удалена.', $slug));
    }
    
    return $this->render('StoreBundle:News:show.html.twig', array(
      'news' => $news  
    ));
  }

  public function  _sidebarAction()
  {
    $recentNews = $this->getDoctrine()
                       ->getRepository('StoreBundle:Text\News')
                       ->findRecent(3);

    return $this->render('StoreBundle:News:sidebar.html.twig', array(
      'recentNews' => $recentNews
    ));
  }
}
