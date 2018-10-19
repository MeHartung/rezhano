<?php

namespace StoreBundle\Repository\Store\Logistics\Delivery\Cdek;

use Doctrine\ORM\EntityRepository;
use StoreBundle\Entity\Store\Logistics\Delivery\Cdek\CdekCity;
use StoreBundle\Service\Geography\Location;
use StoreBundle\Service\Geography\PresenceMap;

/**
 * CdekCityRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CdekCityRepository extends EntityRepository
{
  private $cityRegionList = null;
          
  public function getNbPostomats(CdekCity $city)
  {
    return $this->getEntityManager()
              ->createQueryBuilder()
                 ->select('COUNT(l)')
                 ->from('StoreBundle:Store\Logistics\Delivery\Cdek\CdekRawPvzlist', 'l')
                 ->where('l.city_code = :city_code')
                 ->andWhere('l.type = :type')
                 ->setParameters([
                   ':city_code' => $city->getCode(),
                   ':type' => 'POSTOMAT'
                 ])
              ->getQuery()
              ->setMaxResults(1)
              ->getSingleScalarResult();         
  }       
  
  public function getNbPvz(CdekCity $city)
  {    
    return $this->getEntityManager()
              ->createQueryBuilder()
                 ->select('COUNT(l)')
                 ->from('StoreBundle:Store\Logistics\Delivery\Cdek\CdekRawPvzlist', 'l')
                 ->where('l.city_code = :city_code')
                 ->andWhere('l.type = :type')
                 ->setParameters([
                   ':city_code' => $city->getCode(),
                   ':type' => 'PVZ'
                 ])                 
              ->getQuery()
              ->setMaxResults(1)
              ->getSingleScalarResult();         
  }
  
  /**
   * Возвращает список регионов, отсортированный по алфавиту
   * 
   * @return String[]
   */
  public function getKnownRegionNames()
  {
    return array_map('current', $this->getEntityManager()
              ->createQueryBuilder()
                 ->select('c.region')
                 ->from('StoreBundle:Store\Logistics\Delivery\Cdek\CdekCity', 'c')
                 ->where('c.region IS NOT NULL')
                 ->groupBy('c.region')
                 ->orderBy('c.region')
              ->getQuery()
              ->getScalarResult());       
  }
  
  /**
   * Возвращает список городов в заданном регионе.
   * 
   * Если какой-либо из городов является городом присутствия, задает ему код, равный ключу 
   * в списке карты присутствия (см. \AccurateCommerce\StoreBundle\Service\Geography\Location::getPresenceMap)
   * 
   * @param String $region
   * @return CdekCity[]
   */
  public function getCitiesForRegion($region)
  {
    $cities = $this->createQueryBuilder('c')
                ->where('c.region = :region')
                ->setParameter(':region', (string)$region)
                ->orderBy('c.name')
                ->getQuery()
                ->getResult();  
    
//    $presenceMap = new PresenceMap();
//    $presenceMap = $presenceMap->getAll();
//
//    foreach ($cities as $city)
//    {
//      foreach ($presenceMap as $code => $entry)
//      {
//        if ($city->getName() == $entry['name'])
//        {
//          $city->setCode($code);
//          break;
//        }
//      }
//    }
    
    return $cities;
  }
  
  /**
   * Возвращает название региона для города 
   * 
   * Данные берутся из файла городов СДЭК
   * 
   * @param CdekCity $city
   * @return String
   */
  public function getRegionNameFromCsv(CdekCity $city)
  {
    if (null === $this->cityRegionList)
    {
      $this->loadCityRegionList();
    }
    foreach ($this->cityRegionList as $entry)
    {
      if ($entry[0] == $city->getCode())
      {
        return $entry[2];
      }
    }
    
    return null;
  }
  
  /**
   * Загружает "список городов по базе СДЭК" (это у них на их дебильном сайте так называется)
   */
  protected function loadCityRegionList()
  {
    $filename = dirname(__FILE__) . '/../../../../../../../var/cdekcatalogue/cities.csv';
    $regions = array();
    if (($handle = fopen($filename, "r")) !== FALSE)
    {
      while (($data = fgetcsv($handle, 1000, "\t")) !== FALSE)
      {
        $num = count($data);
        if (3 == $num)
        {
          $regions[] = array($data[0], $data[1], $data[2]);
        }
      }
      fclose($handle);
    }
    $this->cityRegionList = $regions;
  }

}