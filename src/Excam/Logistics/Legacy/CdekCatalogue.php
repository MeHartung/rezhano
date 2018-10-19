<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace Excam\Logistics\Legacy;


use AccurateCommerce\Component\CdekShipping\Api\CdekApiClient;
use StoreBundle\Entity\Store\Logistics\Delivery\Cdek\CdekRawPvzlist;
use StoreBundle\Service\Geography\Location;

class CdekCatalogue
{
  const MOSCOW_REGION_ID = 1;
  const RADONEZ_POINT_CODE = 'MSK2';

  private $location;

  private $kernelRootDir;

  private $cdekApi;

  public function __construct(Location $location, $kernelRootDir, CdekApiClient $apiClient)
  {
    $this->location = $location;
    $this->kernelRootDir = $kernelRootDir;
    $this->cdekApi = $apiClient;
  }

  private $main_regions = array(
    1 => 'Москва',
    'Московская обл.',
    'Санкт-Петербург',
    'Ленинградская обл.',
    'Владимирская обл.',
    'Ярославская обл.',
    'Новгородская обл.',
    'Нижегородская обл.',
    'Курская обл.',
    'Тульская обл.',
    'Оренбургская обл.',
    'Свердловская обл.',
    'Челябинская обл.',
    'Тюменская обл.',
    'Новосибирская обл.',
    'Красноярский край',
    'Иркутская обл.',
    'Адыгея респ.',
    'Алтай респ.',
    'Алтайский край',
    'Амурская обл.',
    'Архангельская обл.',
    'Астраханская обл.',
    'Башкортостан респ.',
    'Белгородская обл.',
    'Брянская обл.',
    'Бурятия респ.',
    'Волгоградская обл.',
    'Вологодская обл.',
    'Воронежская обл.',
    'Дагестан респ.',
    'Забайкальский край',
    'Ивановская обл.',
    'Кабардино-Балкарская респ.',
    'Калининградская обл.',
    'Калужская обл.',
    'Камчатский край',
    'Карачаево-Черкесская респ.',
    'Карелия респ.',
    'Кемеровская обл.',
    'Кировская обл.',
    'Коми респ.',
    'Костромская обл.',
    'Краснодарский край',
    'Крым респ.',
    'Курганская обл.',
    'Липецкая обл.',
    'Магаданская обл.',
    'Мордовия респ.',
    'Мурманская обл.',
    'Ненецкий авт. округ',
    'Омская обл.',
    'Орловская обл.',
    'Пензенская обл.',
    'Пермский край',
    'Приморский край',
    'Псковская обл.',
    'Ростовская обл.',
    'Рязанская обл.',
    'Самарская обл.',
    'Саратовская обл.',
    'Саха респ. (Якутия)',
    'Сахалинская обл.',
    'Северная Осетия респ.',
    'Смоленская обл.',
    'Ставропольский край',
    'Тамбовская обл.',
    'Татарстан респ.',
    'Тверская обл.',
    'Томская обл.',
    'Удмуртия респ.',
    'Ульяновская обл.',
    'Хабаровский край',
    'Хакасия респ.',
    'Ханты-Мансийский авт. округ',
    'Чеченская респ.',
    'Чувашия респ.',
    'Ямало-Ненецкий авт. округ'
  );

  function getCurrentRegion()
  {
    $region_id = NULL;
    // задан конкретный регион
    if (isset($_GET['region_id']))
    {
      $region_id = filter_input(INPUT_GET, 'region_id', FILTER_VALIDATE_INT);
    } else
    {
      //Используем выбранный пользователем регион

      $location = $this->location;

      $region_id = array_search($location->getRegionName(), $this->main_regions);
    }

    if (!$region_id)
    {
      $region_id = self::MOSCOW_REGION_ID;
    }

    return $region_id;
  }

  function getMoscowRegionId()
  {
    return self::MOSCOW_REGION_ID;
  }

  function getRadonezPointCode()
  {
    return self::RADONEZ_POINT_CODE;
  }

  function getMainRegions()
  {
    return $this->main_regions;
  }

  function getRegionsWithCities()
  {
    $filename = $this->kernelRootDir . '/../var/cdekcatalogue/cities.csv';
    $regions = array();
    if (($handle = fopen($filename, "r")) !== FALSE)
    {
      while (($data = fgetcsv($handle, 1000, "\t")) !== FALSE)
      {
        $num = count($data);
        if (3 == $num)
        {
          $regions[$data[2]][] = array('id' => $data[0], 'name' => $data[1]);
        }
      }
      fclose($handle);
    }
    return $regions;
  }

  public function getPoints($cities)
  {
    $found_cities = array();
    $found_points = array();
    foreach ($cities as $city)
    {
      $pvzList = $this->cdekApi->getPvzList($city['id']);

      if (!empty($pvzList))
      {
        foreach ($pvzList as $point)
        {
          /** @var CdekRawPvzlist $point */
          $found_points[] = $point;
        }

        $found_cities[] = $city;
      }
    }
    if ($this->getCurrentRegion() == self::MOSCOW_REGION_ID)
    {
      $found_points = array_reverse($found_points);
    }
    return array($found_cities, $found_points);
  }
}