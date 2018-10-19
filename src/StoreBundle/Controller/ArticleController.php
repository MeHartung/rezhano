<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Controller;


use StoreBundle\Entity\Store\Logistics\Delivery\Cdek\CdekCity;
use StoreBundle\Entity\Text\Article;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class ArticleController extends Controller
{
  public function indexAction()
  {
    $articles = $this->getDoctrine()->getManager()
      ->createQuery('SELECT a FROM StoreBundle:Text\Article a')
      ->getResult()
      ;

    if (!$articles)
    {
      throw $this->createNotFoundException('Ничего не найдено');
    }

    return $this->render('StoreBundle:Article:index.html.twig',
      [
        'articles' => $articles
      ]);
  }

  public function showAction($slug)
  {
    $article = $this->getDoctrine()
                    ->getRepository('StoreBundle:Text\Article')
                    ->findOneBy(array('slug' => $slug));

    if (!$article)
    {
      throw $this->createNotFoundException(sprintf('Статья "%s" не найдена', $slug));
    }

    $article = $this->replaceShortcodes($article);

    return $this->render('StoreBundle:Article:show.html.twig', array(
      'article' => $article
    ));
  }

  public function restShowAction($slug)
  {
    $article = $this->getDoctrine()
      ->getRepository('StoreBundle:Text\Article')
      ->findOneBy(array('slug' => $slug));

    if (!$article)
    {
      throw $this->createNotFoundException(sprintf('Статья "%s" не найдена', $slug));
    }

    return new JsonResponse(array(
      'slug' => $article->getSlug(),
      'title' => $article->getTitle(),
      'text' => $article->getText()
    ));
  }

  /**
   * @param $article Article
   * @return Article
   */
  private function replaceShortcodes($article)
  {
    /**
     * @var $city CdekCity
     */
//    $city = $this->get('store.geography.location')->getCityName();
//
//    if ($city == 'Екатеринбург')
//    {
//      $cityKey = 'ekb';
//    } else
//    {
//      $cityKey = 'moskow';
//    }

    $cityKey = 'ekb';
   // $phone = $this->get('store.geography.location')->getContactPhoneByCity($cityKey);

    $phone = 0;
    if ($cityKey = 'moskow')
    {
      $phone = '<span class="call_phone_1">' . $phone . '</span>';
    }

    if ($cityKey = 'ekb')
    {
      $phone = '<span class="call_phone_2">' . $phone . '</span>';
    }

    $article->setText(strtr(
      $article->getText(),
      [
        '%customer_phone%' => $phone,
        '%customer_phone_text%' => '<span class="call_phone_text">' .$phone. '</span>']

    ));

    return $article;
  }
}