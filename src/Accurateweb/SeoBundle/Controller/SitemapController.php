<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 26.09.2017
 * Time: 18:32
 */

namespace Accurateweb\SeoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SitemapController extends Controller
{
  public function indexAction()
  {
    $sitemap = $this->get('aw_seo.sitemap.builder')->build();

    $response =  $this->render('@AccuratewebSeo/Sitemap/sitemap.xml.twig', array(
      'urls' => $sitemap->getUrls()
    ));
    $response->headers->add(array(
      'Content-Type' => 'text/xml'
    ));

    return $response;
  }
}