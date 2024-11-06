<?php

/*
 *  @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */

namespace AccurateCommerce\Search;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use AccurateCommerce\Search\Sphinx\Index\SphinxIndexCatalogSections;
use AccurateCommerce\Search\Sphinx\SphinxClient;
use StoreBundle\Repository\Store\Catalog\Taxonomy\TaxonRepository;

/**
 * Поиск по разделам каталога
 *
 * @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
class CatalogSectionSearch extends CatalogSearch
{

  /**
   * Конструктор.
   * 
   * @param String $query Поисковый запрос
   */
  public function __construct(SphinxClient $sphinxClient, $query)
  {
    parent::__construct($sphinxClient, $query);
    
    $this->addIndex(new SphinxIndexCatalogSections());
  }
  
  /**
   * Создает новый экземпляр CatalogSectionSearch
   * 
   * @param String $query Поисковый запрос
   * 
   * @return CatalogSectionSearch
   */
  static public function create(SphinxClient $sphinxClient, $query)
  {
    return new CatalogSectionSearch($sphinxClient, $query);
  }
  
  /**
   * Возвращает коллекцию объектов, удовлетворяющих условиям поиска
   * 
   * @return ArrayCollection
   */
  public function getObjects(QueryBuilder $queryBuilder)
  {
    if (!count($this->getObjectIds()))
    {
      return new ArrayCollection();
    }
    
    $catalogSectionToSearchIn = $this->getCatalogSection();

    $qb = $queryBuilder;

    $catalogSections =
        $qb->where($qb->expr()->in('t.id', $this->getObjectIds()))
           ->andWhere($qb->expr()->gt('t.treeLeft', 1))
//                  ->_if($catalogSectionToSearchIn)
//                    ->descendantsOf($catalogSectionToSearchIn)
//                  ->_endif()
            ->getQuery()
            ->getResult();
    
    $weightMap = $this->getWeightMap();
    foreach ($catalogSections as $catalogSection)
    {
      if (isset($weightMap[$catalogSection->getId()]))
      {
        $catalogSection->setSphinxWeight($weightMap[$catalogSection->getId()]);
      }
    }
    
    if ($this->sortByRelevance)
    {
      $this->sortByRelevance($catalogSections);
    }    
    
    return new ArrayCollection($catalogSections);
    
  }
  
  protected function doSearch($query)
  {    
    /*
     * Если поисковый запрос пустой или состоит из одних пробелов, поиск не выполняется
     */
    if (!strlen(trim($query)))
    {
      return array();
    }
    
    $indexNames = array();
    foreach ($this->getIndexes() as $index)
    {
      $indexNames[] = $index->getSphinxIndex(); 
    }
    
    $indicesToSearch = implode(' ', $indexNames);
    
    $sphinxSearch = $this->getSphinxClient();
    $this->configureSphinxClient($sphinxSearch);    

    //$sphinxSearch->SetMatchMode(SphinxClient::SPH_MATCH_EXTENDED);
    $sphinxSearch->SetRankingMode(SphinxClient::SPH_RANK_SPH04);
        
    //Попытаемся поискать сразу с учетом замен из словаря замен
    $correctedQuery = $this->correctQuery($query);
    $_query = '("'.$sphinxSearch->EscapeString($query).'"*)';
    if ($correctedQuery != $query)
    {
      $_query .= ' | ("'.$sphinxSearch->EscapeString($correctedQuery).'"*)';
    }    
    
    $sphinxResults = $this->query($sphinxSearch, $_query, $indicesToSearch);    

    $matches = array();
    if (isset($sphinxResults['matches']))
    {
      $matches = $sphinxResults['matches'];
    } 

    return $matches;
  }  
  
  public function sortByRelevance(&$objects)
  {
    usort($objects, function ($_a, $_b){
      $a = $_a->getSphinxWeight();
      $b = $_b->getSphinxWeight();
      
      if ($a == $b) 
      {
        if ($_a->getTreeLevel() == $_b->getTreeLevel())
        {
            return $_a->compare($_b);
        }
        return ($_a->getTreeLevel() < $_b->getTreeLevel()) ? -1 : 1; 
      }
      else
      {
        return ($a < $b) ? 1 : -1;
      }
    });

  }
}
