<?php

/*
 * Автор Денис Н. Рагозин <dragozin at accurateweb.ru>
 */

namespace StoreBundle\Controller\Logistics;

use AccurateCommerce\DataAdapter\ClientApplicationModelCollection;
use App\Logistics\Legacy\CdekCatalogue;
use StoreBundle\DataAdapter\CityClientModelAdapter;
use StoreBundle\DataAdapter\LocationClientModelAdapter;
use StoreBundle\DataAdapter\RegionClientModelAdapter;
use StoreBundle\Repository\Store\Logistics\Delivery\Cdek\CdekCityRepository;
use AccurateCommerce\Util\EndingFormatter;
use StoreBundle\Service\Geography\Location;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 *
 * @author Денис Н. Рагозин <dragozin at accurateweb.ru>
 */
class GeographyController extends Controller
{
  public function headerAddressAction()
  {
    $nbPostomats = null;
    $nbPvz = null;

    $location = $this->get('store.geography.location');
    /* @var $location Location */

    $city = $location->getCdekCity();
    $phoneType = 'ekb';

    if ($city)
    {

      $repo = $this->getDoctrine()->getRepository('StoreBundle:Store\Logistics\Delivery\Cdek\CdekCity');

      /* @var $repo CdekCityRepository */
      $nbPvz = $repo->getNbPvz($city);
      $nbPostomats = $repo->getNbPostomats($city);

      if ($city->getName() == 'Москва')
      {
        $phoneType = 'msk';
      }
    }

    return $this->render('StoreBundle:Common\header:address.html.twig', [
      'type' => $phoneType,
      'phone' => $location->getContactPhone(),
      'address' => $location->getAddress(),
      'isPresenceCity' => $location->isPresenceCity(),
      'nbPvz' => $nbPvz,
      'nbPvzStr' => !empty($nbPvz) ? EndingFormatter::format($nbPvz, ['пункт', 'пункта', 'пунктов']).' выдачи' : null,
      'nbPostomats' => $nbPostomats,
      'nbPostomatStr' => !empty($nbPostomats) ? EndingFormatter::format($nbPvz, ['почтомат', 'почтомата', 'почтоматов']) : null,
    ]);
  }

  public function citySelectorLayerAction()
  {
    $location = $this->get('store.geography.location');
    /* @var $location Location */

    $alias = $location->getAlias();
    $repo = $this->getDoctrine()->getRepository('StoreBundle:Store\Logistics\Delivery\Cdek\CdekCity');
    /* @var $repo CdekCityRepository */
    $regions = $repo->getKnownRegionNames();
    $cities = [];

    if (!$regions)
    {
      $selectedRegion = '';
    }
    else
    {
      $selectedRegion = $regions[0];
    }

    $city = $location->getCdekCity();

    if ($city && null !== $city->getRegion())
    {
      $selectedRegion = $city->getRegion();
    }

    if ($selectedRegion)
    {
      $cities = $repo->getCitiesForRegion($selectedRegion);
    }

    return $this->render('StoreBundle:Common\header:city-select-layer.html.twig', [
      'regions' => $regions,
      'region' => $selectedRegion,
      'cities' => $cities,
      'cityCode' => $alias,
      'cityClientModels' => ClientApplicationModelCollection::createAdaptedCollection($cities, CityClientModelAdapter::class),
      'regionClientModels' => ClientApplicationModelCollection::createAdaptedCollection($regions, RegionClientModelAdapter::class),
      'location' => new LocationClientModelAdapter($location)
    ]);
  }

  public function citySelectorLinkAction()
  {
    $location = $this->get('store.geography.location');

    return $this->render('StoreBundle:Common/header:city-selector-link.html.twig', array(
      'isConfirmed' => $location->isConfirmed(),
      'cityName' => $location->getCityName(),
      'alias' => $location->getAlias()
    ));
  }

  public function cityListAction(Request $request)
  {
    $cities = [];

    $repo = $this->getDoctrine()->getRepository('StoreBundle:Store\Logistics\Delivery\Cdek\CdekCity');

    $location = $this->get('store.geography.location');
    $alias = $location->getAlias();

    $region = $request->get('region');
    if (!$region)
    {
      throw $this->createNotFoundException('Это действие возможно только с указанием региона. Параметр "region" не задан в запросе.');
    }

    $cityEntities = $repo->getCitiesForRegion($region);
    foreach ($cityEntities as $cityEntity)
    {
      $cities[] = [
        'code' => $cityEntity->getCode(),
        'name' => $cityEntity->getName(),
        'selected' => $cityEntity->getCode() == $alias
      ];
    }

    return new JsonResponse($cities);
  }

  public function contactsAction()
  {
//    $legacyCdekCatalogue = new CdekCatalogue(
//      $this->get('store.geography.location'),
//      $this->getParameter('kernel.root_dir'),
//      $this->get('accuratecommerce.cdek.api')
//    );
//
//    $phoneMsk = $this->get('store.geography.location')->getContactPhoneByCity('msk');
//    $phoneEkb = $this->get('store.geography.location')->getContactPhoneByCity('ekb');

    return $this->render('@Store/Contacts/index.html.twig', array(
//      'mainRegions' => $legacyCdekCatalogue->getMainRegions(),
//      'current_region_id' => $legacyCdekCatalogue->getCurrentRegion(),
//      'phoneMsk' => $phoneMsk,
//      'phoneEkb' => $phoneEkb
    ));
  }

  public function contactsCityListAction()
  {
    $legacyCdekCatalogue = new CdekCatalogue(
      $this->get('store.geography.location'),
      $this->getParameter('kernel.root_dir'),
      $this->get('accuratecommerce.cdek.api')
    );

    $current_region_id = $legacyCdekCatalogue->getCurrentRegion();
    $main_regions = $legacyCdekCatalogue->getMainRegions();
    $mocow_region_id = $legacyCdekCatalogue->getMoscowRegionId();
    $regions = $legacyCdekCatalogue->getRegionsWithCities();
    $current_region = $main_regions[$current_region_id];
    list($found_cities, $found_points) = $legacyCdekCatalogue->getPoints($regions[$current_region]);

    return $this->render('@Store/Contacts/pickup_points.html.twig', array(
      'mocow_region_id' => $mocow_region_id,
      'found_cities' => $found_cities,
      'found_points' => $found_points
    ));
  }
}
