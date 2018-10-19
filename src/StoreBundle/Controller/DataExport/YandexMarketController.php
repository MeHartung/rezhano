<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 18.09.2017
 * Time: 22:30
 */

namespace StoreBundle\Controller\DataExport;


use StoreBundle\DataExport\YandexMarketXmlBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class YandexMarketController extends Controller
{
  public function exportAction()
  {
    $yaMarketXmlBuilder = $this->get('store.data_export.yandexmarket');
    $response = new Response($yaMarketXmlBuilder->build());
    $response->headers->add(array(
      'Content-Type' => 'text/xml'
    ));

    return $response;
  }
}