<?php

/*
 *  @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */

namespace AccurateCommerce\Search;

use AccurateCommerce\Exception\SearchException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use AccurateCommerce\Search\Sphinx\Index\SphinxIndexProducts;
use AccurateCommerce\Search\Sphinx\SphinxClient;

/**
 * Description of ProductSearch
 *
 * @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
class ProductSearch extends CatalogSearch
{
  private $searchArchiveProducts = false;

  private $searchPublishedProductsOnly = true;

  public function __construct(SphinxClient $sphinxClient, $query)
  {
    parent::__construct($sphinxClient, $query);
    
    $this->addIndex(new SphinxIndexProducts());
  }
  
  /**
   * Создает новый экземпляр ProductSearch
   * 
   * @param String $query
   * @return ProductSearch
   */
  static public function create(SphinxClient $sphinxClient, $query)
  {
    return new ProductSearch($sphinxClient, $query);
  }
  
  protected function configureSphinxClient(SphinxClient $client)
  {
    parent::configureSphinxClient($client);
    
    $client->SetFieldWeights(array(
        'sku' => 10,
        //'model' => 10,
        'name' => 1
    ));    
  }
  
  
  /**
   * Возвращает коллекцию найденных товаров.
   * 
   * Если сортировка по релевантности включена, товары в коллекции будут отсортированы по релевантности.
   * В противном случае товары будут следовать в неопределенном порядке.
   * 
   * @return ArrayCollection
   */
  public function getObjects(QueryBuilder $queryBuilder)
  {
    if (!$this->getObjectIds())
    {
      return new ArrayCollection();
    }

    $products = $this->getProductQuery($queryBuilder)
                     ->getResult();

    $weightMap = $this->getWeightMap();
    foreach ($products as $product)
    {
      /* @var $product Product */
      if (isset($weightMap[$product->getId()]))
      {
        $product->setSphinxWeight($weightMap[$product->getId()] + ($product->isPurchasable() ? 100000 : 0));
      }
    }
    
    if ($this->sortByRelevance)
    {
      $this->sortByRelevance($products);
    }    
    
    return new ArrayCollection($products);
  }

  /**
   * @param QueryBuilder $queryBuilder
   * @return QueryBuilder
   */
  public function buildQuery(QueryBuilder $queryBuilder)
  {
//    $catalogSection = $this->getCatalogSection();

    if (!count($this->getObjectIds()))
    {
      throw new SearchException('No results');
    }

    $queryBuilder->andWhere($queryBuilder->expr()->in('p.id', $this->getObjectIds()))
      ->groupBy('p.id');

    if ($this->searchPublishedProductsOnly)
    {
      $queryBuilder->andWhere($queryBuilder->expr()->eq('p.published',  true));
    }

//    $query = ProductQuery::create(null, $c)
//                    ->useBindingQuery()
//                      ->filterByProductId($this->getObjectIds(), Criteria::IN)
//                      ->_if($catalogSection)
//                        ->useCatalogSectionQuery()
//                          ->descendantsOf($catalogSection)
//                        ->endUse()
//                      ->_endif()
//                    ->endUse()
//                    ->_if($this->searchPublishedProductsOnly)
//                      ->filterByPublished(true)
//                    ->_endif()
//                    ->groupById();

//    if ($this->searchArchiveProducts)
//    {
//      $query->disableSoftDelete();
//    }

    return $queryBuilder;
  }

  /**
   * Возвращает запрос на выборку товаров для найденных результатов
   * 
   * @return Query
   */
  public function getProductQuery(QueryBuilder $queryBuilder)
  {
    $qb = $this->buildQuery($queryBuilder);

    return $qb->getQuery();
  }
  
  protected function doSearch($query)
  {
    $matches = null;  
    /*
     * Если поисковый запрос пустой или состоит из одних пробелов, поиск не выполняется
     */
    if (strlen(trim($query)))
    {
      $indexNames = array();
      foreach ($this->getIndexes() as $index)
      {
        $indexNames[] = $index->getSphinxIndex(); 
      }

      $indicesToSearch = implode(' ', $indexNames);

      $sphinxSearch = $this->getSphinxClient();
      $this->configureSphinxClient($sphinxSearch);    

      //$sphinxSearch->SetMatchMode(SphinxClient::SPH_MATCH_EXTENDED);
      $sphinxSearch->SetRankingMode(SphinxClient::SPH_RANK_WORDCOUNT);
      
      //Этап 1 - Ищем для всех посетителей товары из числа опубликованных и не архивных
      $sphinxSearch->SetFilter('is_deleted', [0]);
      
      //Сначала ищем полные совпадения SKU
      $sphinxResults = $this->query($sphinxSearch, sprintf('@sku "^%1$s$" | @* (%1$s)', $sphinxSearch->EscapeString($query)), $indicesToSearch);    
  //    $sphinxSearch->SetMatchMode(sfSphinxClient::SPH_MATCH_ALL);
  //    $sphinxResults = $this->query($sphinxSearch, $query, $indicesToSearch);


      if (isset($sphinxResults['matches']))
      {
        $matches = $sphinxResults['matches'];
      } 
      if (null === $matches)
      {
        //$sphinxSearch->SetMatchMode(SphinxClient::SPH_MATCH_EXTENDED);
        $sphinxSearch->SetRankingMode(SphinxClient::SPH_RANK_SPH04);

        //Попробуем поискать частичные совпадения для случаев, когда последнее слово введено не полностью
        //При этом попытаемся поискать также с учетом замен из словаря замен
        $correctedQuery = $this->correctQuery($query);
        $_query = '("'.$sphinxSearch->EscapeString($query).'"*)';
        if ($correctedQuery != $query)
        {
          $_query .= ' | ("'.$sphinxSearch->EscapeString($correctedQuery).'"*)';
        }
        
        $sphinxResults = $this->query($sphinxSearch, $_query, $indicesToSearch);
        if (isset($sphinxResults['matches']))
        {
          $matches = $sphinxResults['matches'];
        } 
      }
      if (null === $matches && isset($sphinxResults['words']))
      {

        $queries = array();
        foreach ($sphinxResults['words'] as $word => $hash)
        {
          if (mb_strlen($word, 'UTF-8') > 1 && isset($hash['docs']) && $hash['docs'] > 0)
          {
            $queries[] = $sphinxSearch->EscapeString($word);
          }
        }
        if (!empty($queries))
        {
          $sphinxQuery = '('.implode('|',$queries).')';
          $sphinxResults = $this->query($sphinxSearch, $sphinxQuery, $indicesToSearch);
          if (strlen($sphinxResults['error']))
          {
            throw new SphinxSearchException($sphinxResults['error']);
          }

          if (isset($sphinxResults['matches']))
          {
            $matches = $sphinxResults['matches'];
          }
        }
      }    
    }
    //Этап 2 - для администраторов ищем по всем товарам по названию товара и sku
    if (null === $matches && $this->searchArchiveProducts)
    {
      $sphinxSearch->ResetFilters();
      //$sphinxSearch->SetFilter('is_deleted', [0, 1]);
      
      //Сначала ищем полные совпадения SKU
      $sphinxResults = $this->query($sphinxSearch, sprintf('@sku "^%1$s$" | @* (%1$s)', $sphinxSearch->EscapeString($query)), $indicesToSearch);    
      if (isset($sphinxResults['matches']))
      {
        $matches = $sphinxResults['matches'];
      } 
    }
    
    if (null === $matches)
    {
      //Ничего не найдено, се ля ви...
      $matches = array();
    }
    return $matches;
  }  
  
  /**
   * Указывает, следует ли учитывать при поиске архивные товары
   *
   * @param boolean $v
   * @return ProductSearch
   */
  public function setSearchArchiveProducts($v)
  {
    $this->searchArchiveProducts = (bool)$v;
    
    return $this;
  }

  /**
   * Указывает, следует ли учитывать при поиске товары, снятые с публикации
   *
   * @param $v
   * @return $this
   */
  public function setSearchPublishedProductsOnly($v)
  {
    $this->searchPublishedProductsOnly = (bool)$v;

    return $this;
  }
}
