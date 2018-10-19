<?php

/*
 *  @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */

namespace AccurateCommerce\Search;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use AccurateCommerce\Search\Sphinx\Exception\SphinxSearchException;
use AccurateCommerce\Search\Sphinx\Index\SphinxIndexBase;
use AccurateCommerce\Search\Sphinx\SphinxClient;
use StoreBundle\Entity\Store\Catalog\Taxonomy\Taxon;

/**
 * Поиск по каталогу
 *
 * @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
abstract class CatalogSearch
{
 
  private $query,
          $matches,
          /** @var SphinxIndexBase[] */
          $indexes,
          $catalogSection,
          $limit,
          $tryKeyboardLayoutCorrection,
          $lastExecutedQuery;

  private $layoutMap = array(
            'en' => array(
  '`', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '-', '=', 'q', 'w', 'e', 'r', 't', 'y', 'u', 'i', 'o', 'p', '[', 
  ']', 'a', 's', 'd', 'f', 'g', 'h', 'j', 'k', 'l', ';', '\'', 'z', 'x', 'c', 'v', 'b', 'n', 'm', ',', '.', '/', '~', '!', 
  '@', '#', '$', '%', '^', '&', '*', '(', ')', '_', '+', 'Q', 'W', 'E', 'R', 'T', 'Y', 'U', 'I', 'O', 'P', '{', '}', 'A', 
  'S', 'D', 'F', 'G', 'H', 'J', 'K', 'L', ':', '"', 'Z', 'X', 'C', 'V', 'B', 'N', 'M', '<', '>', '?'),
            'ru' => array(
  'ё', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '-', '=', 'й', 'ц', 'у', 'к', 'е', 'н', 'г', 'ш', 'щ', 'з', 'х', 
  'ъ', 'ф', 'ы', 'в', 'а', 'п', 'р', 'о', 'л', 'д', 'ж', 'э', 'я', 'ч', 'с', 'м', 'и', 'т', 'ь', 'б', 'ю', '.', 'Ё', '!', 
  '"', '№', ';', '%', ':', '?', '*', '(', ')', '_', '+', 'Й', 'Ц', 'У', 'К', 'Е', 'Н', 'Г', 'Ш', 'Щ', 'З', 'Х', 'Ъ', 'Ф', 
  'Ы', 'В', 'А', 'П', 'Р', 'О', 'Л', 'Д', 'Ж', 'Э', 'Я', 'Ч', 'С', 'М', 'И', 'Т', 'Ь', 'Б', 'Ю', ',')
          );

  
  protected $sortByRelevance;

  /**
   * Конструктор.
   * 
   * @param String $query
   */
  public function __construct(SphinxClient $sphinxClient, $query)
  {
    $this->query = $query;
    $this->indexes = array();    
    
    $this->tryKeyboardLayoutCorrection = true;
    
    $this->setSortByRelevance(true);

    $this->sphinxClient = $sphinxClient;
    $this->configureSphinxClient($this->sphinxClient);
  }
  
//  /**
//   * Создает новый экземпляр CatalogSearch.
//   *
//   * @param String $query Поисковый запрос
//   * @return CatalogSearch
//   */
//  static public function create($query)
//  {
//    return new self($query);
//  }
  
  /**
   * Добавляет индекс к поиску
   * 
   * @param SphinxIndexBase $index Индекс, по которому будет производиться поиск
   * @return CatalogSearch Ссылка на себя для цепочек вызовов
   */
  public function addIndex(SphinxIndexBase $index)
  {
    $this->indexes[] = $index;
    
    return $this;
  }
  
  /**
   * Выполняет поиск
   * 
   * @throws SphinxSearchException
   */
  public function execute()
  {
    $query = $this->getSphinxQuery();
    $matches = $this->doSearch($query);

    if (empty($matches) && $this->tryKeyboardLayoutCorrection)
    {
      $correctedQueryEnRu = $this->convertKeyboardLayout($query, 'en', 'ru');
      if ($correctedQueryEnRu != $query)
      {
        $matches = $this->doSearch($correctedQueryEnRu);
      }
      
      if (empty($matches))
      {
        $correctedQueryRuEn = $this->convertKeyboardLayout($query, 'ru', 'en');
        if ($correctedQueryRuEn != $query)
        {
          $matches = $this->doSearch($correctedQueryRuEn);
        }
      }            
    }
    
    $this->matches = $matches;    
    
    return $this;
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
      foreach ($this->indexes as $index)
      {
        $indexNames[] = $index->getSphinxIndex(); 
      }

      $indicesToSearch = implode(' ', $indexNames);

      $sphinxSearch = $this->getSphinxClient();

      //$sphinxSearch->SetMatchMode(sfSphinxClient::SPH_MATCH_ALL);
      //Сначала ищем полные совпадения фразы
      $sphinxResults = $this->query($sphinxSearch, $query, $indicesToSearch);    


      if (isset($sphinxResults['matches']))
      {
        $matches = $sphinxResults['matches'];
      } 
      if (null === $matches)
      {
        //$sphinxSearch->SetMatchMode(sfSphinxClient::SPH_MATCH_EXTENDED);
        //Попробуем поискать частичные совпадения для случаев, когда последнее слово введено не полностью
        $sphinxResults = $this->query($sphinxSearch, '"'.$sphinxSearch->EscapeString($query).'"*', $indicesToSearch);
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
          $query = '('.implode('|',$queries).')';
          $sphinxResults = $this->query($sphinxSearch, $query, $indicesToSearch);
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
    if (null === $matches)
    {
      //Ничего не найдено, се ля ви...
      $matches = array();
    }

    return $matches;
  }
  
  /**
   * Задает раздел каталога, в котором будет производиться поиск
   * 
   * @param Taxon $catalogSection
   * @return CatalogSearch
   */
  public function setCatalogSection($catalogSection)
  {
    $this->catalogSection = $catalogSection;
    
    return $this;
  }
  
  protected function configureSphinxClient(SphinxClient $client)
  {    
    if (null !== $this->limit)
    {
      $client->SetLimits(0, $this->limit);
    }
  }
  
  /**
   * Возвращает выражение для передачи в качестве поискового запроса Sphinx
   * 
   * @return String
   */
  public function getSphinxQuery()
  {
    return $this->query;
  }
  
  /**
   * Возвращает раздел каталога, в котором производится поиск
   * 
   * @return Taxon
   */
  public function getCatalogSection()
  {
    return $this->catalogSection;
  }
  
  /**
   * Возвращает идентификатор раздела каталога, в котром производится поиск
   * 
   * @return int
   */
  public function getCatalogSectionId()
  {
    $catalogSection =  $this->getCatalogSection();
    return $catalogSection ? $catalogSection->getId() : null;
  }
  
  /**
   * Устанавливает максимальное количество возвращаемых результатов
   * 
   * @param int $v
   * @return CatalogSearch
   */
  public function setLimit($v)
  {
    $this->limit = (int)$v;
    
    return $this;
  }  
  
  /**
   * Возвращает результаты поиска.
   * 
   * Результаты поиска представляют собой массив, каждый элемент которого имеет следующие поля:
   *  - id     - Идентификатор записи в БД
   *  - weight - Вес записи
   *  - attrs  - Атрибуты записи, заданные в файле конфигурации Sphinx
   * 
   * @return Array
   */
  public function getResults()
  {    
    return $this->matches;
  }
  
  /**
   * Возвращает идентификаторы найденных записей
   * 
   * @return Array
   */
  public function getObjectIds()
  {
    $objectIds = array();
    $matches = $this->getResults();
    
    foreach ($matches as $id => $match)
    {
      $objectIds[] = $id;
    }
    
    return $objectIds;
  }
  
  /**
   * Выполняет сортировку набора объектов по релевантности
   * 
   * @param type $objects
   */
  public function sortByRelevance(&$objects)
  {
    usort($objects, function ($_a, $_b){
      $a = $_a->getSphinxWeight();
      $b = $_b->getSphinxWeight();
      
      return $a == $b ? $_a->compare($_b) : (($a < $b) ? 1 : -1);
    });
  } 
  
  public function getWeightMap()
  {
    $matches = $this->getResults();
    
    $weights = array();
    foreach ($matches as $id => $match)
    {
      $weights[$id] = $match['weight'];
    }
    
    return $weights;
  }
  
  /**
   * Включает или отключает сортировку результатов по релевантности.
   * 
   * @param boolean $v Передайте true, чтобы включить сортировку по релевантности, либо false, чтобы отключить
   * @return ProductSearch Ссылку на себя для поддержки цепочек вызовов
   */
  public function setSortByRelevance($v)
  {    
    $this->sortByRelevance = (bool)$v;
    
    return $this;
  }

  /**
   * Возвращает true, если включена сортировка по релевантности, иначе false
   * 
   * @return boolean
   */
  public function getSortByRelevance()
  {
    return $this->sortByRelevance;
  }
  
  /**
   * Выполняет простое преобразование символов в строке так, как если бы пользователь забыл переключить язык ввода
   * 
   * @param String $s Строка, которую нужно преобразовать
   * @param String $from Раскладка, из которой нужно преобразовать строку
   * @param String $to Раскладка, в которую нужно преобразовать строку
   * @return String
   */
  public function convertKeyboardLayout($s, $from, $to)
  {
    $r = '';
    
    //Эта строчка разбивает входную строку на массив unicode-символов, чтобы можно
    //было обойти всю строку
    $_s = preg_split('//u',$s, -1, PREG_SPLIT_NO_EMPTY);
    
    foreach ($_s as $c)
    {      
      $k = array_search($c, $this->layoutMap[$from]);
      if (false !== $k)       
      {
        $r .= $this->layoutMap[$to][$k];
      }
      else
      {
        $r .= $c;
      }
    }
    
    return $r;
  }  
  
  /**
   * Отправляет запрос Sphinx на поиск по заданным индексам
   * 
   * @param SphinxClient $sphinxClient Экземлпяр клиента sphinx
   * @param String $query Запрос
   * @param String $indices Список индексов в виде строки через пробел
   * @return Array
   * @throws SphinxSearchException
   */
  protected function query(SphinxClient $sphinxClient, $query, $indices)
  {
    $sphinxResults = $sphinxClient->Query($query, $indices);

    if (strlen($sphinxResults['error']))
    {
      throw new SphinxSearchException($sphinxResults['error']);
    }

    $this->lastExecutedQuery = $query;

    return $sphinxResults;
  }
  
  /**
   * Возвращает перечень индексов, по которым будет производиться поиск
   * 
   * @return String[]
   */
  protected function getIndexes()
  {
    return $this->indexes;
  }
  
  /**
   * Заменяет слова в запросе их корректировками из словаря и возвращает откорректированный запрос.
   * 
   * Если ни одной корректировки не произведено, возвращает исходный запрос
   * 
   * @param String $query Исходный запрос
   * @return String Откорректированный запрос
   */
  public function correctQuery($query)
  {
    $nbCorrections = 0;
    
    $words = explode(' ', $query);
    foreach ($words as $i => $word)
    {
      if (mb_strlen($word, 'UTF-8') > 2)
      {
        $corrections = $this->getSpellCorrectionsForQuery($word);
        if (!$corrections->isEmpty())
        {
          $correction = $corrections[0];
          $words[$i] = $correction->getSpellEtalon()->getPhrase();
          $nbCorrections++;
        }
      }
    }
    
    return ($nbCorrections > 0) ? implode(' ', $words) : $query;
  }
  
  /**
   * Склеивает слово поиска со словами корректировок в запрос для поиска с использованием Sphinx
   * @return string[] строка для поиска Sphinx
   */
  public function correctionsQueryForSphinxSearch($query)
  {
    $corrections = $this->getSpellCorrectionsForQuery($query);
    
    $words = array();
    
    if (!$corrections->isEmpty())
    {

      foreach ($corrections as $correction)
      {
        $word = $correction->getPhrase();
        $words[] = $word . ' | ' . $word . '*';
      }
      $word = $correction->getSpellEtalon()->getPhrase();
      $words[] = $word . ' | ' . $word . '*';

      $words = array_diff($words, array($query . ' | ' . $query . '*'));
      $words = array_unique($words);
    }
    return $words;
  }

  /**
   * Возвращает список корректировок поиска для заданного слова или фразы
   * 
   * @return PropelObjectCollection список корректировок к строке запроса
   */
  private function getSpellCorrectionsForQuery($query)
  {
    return new ArrayCollection();
//    $normalizedQuery = mb_convert_case(preg_replace("[^\w\s]", "", $query), MB_CASE_LOWER, 'UTF-8');
//
//    $correction = SpellCorrectionQuery::create()
//        ->filterByPhrase($normalizedQuery)
//        ->joinWith('SpellEtalon')
//        ->findOne();
//
//    $correctedQuery = null;
//    if ($correction)
//    {
//      $correctedQuery = $correction->getSpellEtalon()->getPhrase();
//    }
//
//    return SpellCorrectionQuery::create()
//              ->useSpellEtalonQuery()
//                ->filterByPhrase($normalizedQuery)
//                ->_if ($correctedQuery)
//                  ->_or()
//                  ->filterByPhrase($correctedQuery)
//                ->_endif()
//              ->endUse()
//              ->find();
  }

  /**
   * @param QueryBuilder $queryBuilder
   * @return ArrayCollection
   */
  abstract public function getObjects(QueryBuilder $queryBuilder);
  
  /**
   * Включает или отключает поиск по транслитерированному запросу
   * 
   * @param bool $v Флаг поиска по транслитерированному запросу
   * 
   * @return CatalogSearch Ссылку на собственный экземпляр для поддержки цепочек вызовов
   */
  public function setKeyboardLayoutCorrection($v)
  {
    $this->tryKeyboardLayoutCorrection = (bool)$v;
    
    return $this;
  }
  
  public function removeIndexAt($at)
  {
    unset($this->indexes[$at]);
  }

  /**
   * @return SphinxClient
   */
  public function getSphinxClient()
  {
    return $this->sphinxClient;
  }

  public function buildExcerpts($docs, $options=array())
  {
    $sphinx = $this->getSphinxClient();

    $indices = $this->getIndexes();
    foreach ($indices as $idx)
    {
      $excerpts = $sphinx->BuildExcerpts($docs, $idx->getSphinxIndex(), $this->lastExecutedQuery, array_merge([
        'before_match' => '<span class="highlight">',
        'after_match' => '</span>',
        'html_strip_mode' => 'none',
        //'query_mode' => true
      ], $options));
    }

    return $excerpts;
  }
}
