<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace AccurateCommerce\Store\Catalog\Filter;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use AccurateCommerce\Model\Taxonomy\TaxonInterface;

class ProductFilter extends BaseFilter
{
  const SORT_ASC = 'asc';
  const SORT_DESC = 'desc';
  const SORT_NONE = 'none';

  const NB_PAGES = 9;
  const DEFAULT_PRIMARY_ORDER = self::SORT_ASC;

  private $eavFilter;

  private $view;
  private $sortColumn;
  private $sortColumns;
  private $sortOrder;

  private $maxPerPageChoices;

  /** @var TaxonInterface */
  protected $taxon;



  /**
   * Конструктор.
   *
   * Доступные опции:
   * <ul>
   * <li>can_edit - устанавливает режим редактирования для этого фильтра. Если опция не задана, режим редактирования фильтра
   * будет автоматически включен для пользователя с привилегий администратора</li>
   * </ul>
   * @param int|string $id
   * @param array      $defaults
   * @param array      $options
   */
  public function __construct($id, TaxonInterface $taxon, array $options = array())
  {
    $this->taxon = $taxon;

//    $this->displayMode = (isset($options['can_edit']) && $options['can_edit']) ? CatalogFilter::DISPLAY_EDIT : CatalogFilter::DISPLAY_GENERIC;
//    if(sfContext::hasInstance() && !isset($options['can_edit']))
//    {
//      $this->displayMode = sfContext::getInstance()->getUser()->hasCredential('admin') ? CatalogFilter::DISPLAY_EDIT : CatalogFilter::DISPLAY_GENERIC;
//    }
//    $this->setSortColumns(array(
//      'rank_desc' => 'популярные',
//      'price_asc' => 'дешевые',
//      'price_desc' => 'дорогие',
//      'availability_desc'=> 'в наличии'
//    ));
//
//    $this->setMaxPerPageChoices(sfConfig::get('app_asIShopCatalogPlugin_available_counters', array(16, 32, 64)));

    parent::__construct($id);

    $this->addField(new RangeFilterField('price', array(
      'label' => 'Цена'
    )));
    $this->addField(new DoctrineChoiceFilterField('brand', array(
      'label' => 'Производитель'
    )));


    $qb = $taxon->getProductQueryBuilder();
    /**
     * @var $qb QueryBuilder
     */

    $productAttributes = $qb
      ->innerJoin('p.productAttributeValues', 'pav')
      ->innerJoin('pav.productAttribute', 'pa')
      ->select('pa.id', 'pa.name')
      ->orderBy('pa.name')
      ->groupBy('pa.id')
      ->getQuery()
      ->getArrayResult();

    $this->eavFilter = new EavFilter($productAttributes);

    foreach ($this->eavFilter->getFields() as $field)
    {
      $this->addField($field);
    }

//    $this->view = $this->getDefaultView();
  }

  public function getClientModelId()
  {
    return $this->getId();
  }


  public function getClientModelName()
  {
    return 'Filter';
  }



  public function getClientModelValues($context = null)
  {

    $fieldStates = $this->getFieldStates();
    $fieldSchema = $this->getSchemaAsArray();
    $pager = $this->getPager();
    $nb_links = sfConfig::get('app_asIShopCatalogPlugin_nb_links', self::NB_PAGES);

    $filterValues = array($this->getName() => array());

    $values = $this->getValues();

    foreach($values as $key => $value)
    {
      if(is_array($value))
      {
        foreach($value as $k => $val)
        {
          if($val === null)
            unset($value[$k]);
        }
      }

      if($value !== null )
      {
        $filterValues[$this->getName()][$key] = $value;
      }
    }



    $result = array(
      "sort" => array(
        'column' => $this->getSortColumn(),
        'order' => $this->getSortOrder(),
        'next' => $this->getNextSort($this->getSortColumn(), $this->getSortOrder())
      ),
      'sortColumns' => $this->getSortColumns(),
      "view" => $this->getView(),
      "section" => $this->getCatalogSection()->getClientModelValues(),
      "pagination" => array(
        "pages" => array(
          "last" => $pager->getLastPage(),
          "current" => $pager->getPage(),

        ),
        "per_page" => $this->getMaxPerPage(),
        "links" => $pager->getLinks($nb_links),
        "nbresults" => $pager->getNbResults(),
        "available_per_page" => $this->getMaxPerPageChoices(),
        "nb_links" => $nb_links
      ),
      'defaults' => array(
        'count' => sfConfig::get('app_asIShopCatalogPlugin_default_per_page', 10),
        'column' => $this->getDefaultSortColumn(),
        'order' => $this->getDefaultSortOrder(),
        'view' => $this->getDefaultView()
      ),
      "enabled_views" => $this->getEnabledViews(),
      "gridSize" => $this->getGridSize(),
      'tableSize' => $this->getTableSize(),
      "state" => $fieldStates,
      "schema" => $fieldSchema,
      "filterState" => $filterValues
    );

    return $result;
  }

  protected function getAvailableActionFields()
  {
    if($this->getCatalogSection() === null)
      return array();


    $result = array();

    $hasNew = $this->getCatalogSection()->generateCriteria(ProductQuery::create()->filterByIsNew(true))->count() > 0;
    $hasSale = $this->getCatalogSection()->generateCriteria(ProductQuery::create()->filterByIsSale(true))->count() > 0;


    if($hasNew)
    {
      $result['is_new'] = 'Новинка';
    }

    if($hasSale)
    {
      $result['is_sale'] = 'Скидка';
    }

    return $result;
  }

  /**
   * Возвращает установленное представление каталога
   *
   * @return int Одна из констант BaseCatalogFilter::VIEW_
   */
  public function getView()
  {
    return $this->view;
  }

  /**
   * Устанавливает представление каталога
   *
   * @param int $v Одна из констант BaseCatalogFilter::VIEW_
   */
  public function setView($v)
  {
    if ($this->hasView($v) === false)
      throw new InvalidArgumentException(sprintf('"%s" is not a valid view id. ', $v));

    $this->view = $v;
  }

  /**
   * Возвращает установленный порядок сортировки для заданного столбца.
   *
   * Если столбец не задан, возвращает порядок сортировки столбца, для которого установлен порядок сортировки
   *
   * @param String $column
   * @return String
   */
  public function getSortOrder($column=null)
  {
    $order = self::SORT_NONE;

    if (null === $column || $column == $this->getSortColumn())
    {
      $order = $this->sortOrder;

      if ($column && preg_match('/^([a-z]+)_(asc|desc)$/', $column, $matches))
      {
        $order = $matches[2];
      }
    }

    return $order;
  }

  /**
   * Устанавливает порядок сортировки результатов фильтрации
   *
   * @param String $v Одна из констант BaseCatalogFilter::SORT_
   */
  public function setSortOrder($v)
  {
    $this->sortOrder = $v === self::SORT_NONE ? false : $v;
  }

  /**
   * Возвращает установленный столбец сортировки
   *
   * @return type
   */
  public function getSortColumn()
  {
    return $this->sortColumn;
  }

  /**
   * Задает столбец для сортировки
   *
   * @param String $v
   */
  public function setSortColumn($v)
  {
    $this->sortColumn = $v;
  }

  /**
   * Возвращает категорию каталога для фильтрации
   *
   * @return IVirtualCategory
   */
  public function getCatalogSection()
  {
    return $this->taxon;
  }

  /**
   * Устанавливает категорию для фильтра
   *
   * @param IVirtualCategory $aCatalogSection
   */
  public function setTaxon($aCatalogSection)
  {
    if (!$aCatalogSection instanceof IVirtualCategory)
      throw new InvalidArgumentException("\$category must be an instance of IVirtualCategory");

    //Сбрасываем фильтрацию при переходе в другую категорию
    if ($this->taxon && $aCatalogSection->getId() != $this->taxon->getId())
    {
      $this->reset();
    }

    $this->taxon = $aCatalogSection;

    $this->configure();
  }


  /**
   * Добавляет правила сортировки к выборке
   *
   * @param Criteria $criteria
   */
  protected function addOrderByColumns($criteria)
  {
    if ($this->getSortColumn() != 'title')
    {
      $criteria->addDescendingOrderByColumn(ProductPeer::IS_PURCHASABLE);
    }

    $column = $this->getSortColumn();
    $order = $this->getSortOrder();

    if (preg_match('/^([a-z]+)_(asc|desc)$/', $column, $matches))
    {
      $column = $matches[1];
      $order = $matches[2];
    }

    if ($column && in_array($order, array('asc', 'desc')))
    {
      $customMethod = sprintf('addOrderBy%s', ucfirst($column));
      if(method_exists($this, $customMethod))
        call_user_func(array($this, $customMethod), $criteria, $order);

      elseif (in_array(ucfirst($column), ProductPeer::getFieldNames()))
        $colname = BasePeer::translateFieldname('Product', $column, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_COLNAME);

      if (isset($colname))
      {
        $method = sprintf('add%sendingOrderByColumn', ucfirst($order));
        call_user_func(array($criteria, $method), $colname);
      }
    }
  }

  /**
   * Возвращает следующий способ сортировки для заданного столбца
   *
   * @param String $column
   * @param String $primaryOrder asc|desc|none
   * @return String asc|desc|none
   */
  public function getNextSort($column, $primaryOrder = null)
  {
    $methodName = 'getNextSortFor'.ucfirst($column);
    if (method_exists($this, $methodName))
    {
      return call_user_func(array($this, $methodName), $primaryOrder);
    }

    if (preg_match('/^([a-z]+)_(asc|desc)$/', $column, $matches))
    {
      return $matches[2];
    }

    if (null === $primaryOrder)
    {
      $primaryOrder = $this->getPrimaryOrder($column);
    }

    $order = $primaryOrder;

    if ($column == $this->getSortColumn())
    {
      switch ($this->getSortOrder())
      {
        case 'asc':  $order = ($primaryOrder == 'asc' ? 'desc' : 'none') ; break;
        case 'desc': $order = ($primaryOrder == 'desc' ? 'asc' : 'none'); break;
      }
    }

    return $order;
  }

  /**
   * Возвращает первый порядок сортировки, который должен быть применен при сортировке по заданному столбцу
   *
   * @param String $column Столбец, для которого будет вычислен первый порядок сортировки
   * @return String
   */
  public function getPrimaryOrder($column)
  {
    $methodName = 'getPrimaryOrderFor'.ucfirst($column);
    if (method_exists($this, $methodName))
    {
      return call_user_func(array($this, $methodName));
    }

    return self::DEFAULT_PRIMARY_ORDER;
  }

  /**
   * Возвращает первый порядок сортировки для сортировки по наличию
   *
   * @return String
   */
  public function getPrimaryOrderForAvailability()
  {
    return self::SORT_DESC;
  }

  public function getEnabledViews()
  {
    return sfConfig::get("app_asIShopPlugin_enabled_views", array('grid' => 'Списком', 'table' => 'Таблицей'));
  }

  public function getDefaultView()
  {
    if ($this->taxon)
    {
      $catalogSection = $this->taxon;
      if ($catalogSection instanceof VirtualCatalogSectionChilds)
      {
        $catalogSection = $catalogSection->getCatalogSection();
      }
      /*
       * Если для этого раздела выбрано представление, подразумевающее возможность выбора способа представления,
       * т.е. любое представление, наследующее от представления "Список товаров с фильтром", то
       * значение вида отображения по умолчанию нужно взять из него. В противном случае будем использовать глобальные настройки
       * отображения
       */
      $presentationProvider = new CatalogSectionPresentationProvider();
      try
      {
        $presentation = $catalogSection->getPresentation($presentationProvider);
      }
      catch (PresentationNotFoundException $e)
      {
        $presentation = $presentationProvider->getDefaultPresentation($catalogSection);
      }

      if ($presentation instanceof CatalogSectionPresentationProductList)
      {
        $presentationOptions = $catalogSection->getCatalogSectionPresentationOptions($presentation);

        if (isset($presentationOptions['view']) && in_array($presentationOptions['view'], array_keys($this->getEnabledViews())))
        {
          return $presentationOptions['view'];
        }
      }
    }

    return asConfig::get("default_product_list_view", sfConfig::get("app_asIShopPlugin_default_view",  'grid'));
  }

  public function getDefaultSortColumn()
  {
    return sfConfig::get("app_asIShopPlugin_default_sort_column",  null);
  }

  public function getDefaultSortOrder()
  {
    return sfConfig::get("app_asIShopPlugin_default_sort_order",  null);
  }

  public function hasView($view)
  {
    return array_key_exists($view, $this->getEnabledViews());
  }

  /**
   * Выполняет сборку критерия для SQL-запроса фильтра
   *
   * @param Criteria $criteria
   */
  public function buildQuery(QueryBuilder $queryBuilder)
  {
//    $queryBuilder = $this->repository->createQueryBuilder();
//
//    $this->aCatalogSection->buildQuery($queryBuilder);

    //$this->addOrderByColumns();

    return parent::buildQuery($queryBuilder);
  }

  protected function combineGuids($guid1, $guid2=null, $guid3=null)
  {
    $hash = md5($guid1.($guid2 ? $guid2 : '00000000-0000-0000-0000-000000000000').($guid3 ? $guid3 : '00000000-0000-0000-0000-000000000000'));
    return sprintf('%s-%s-%s-%s-%s',
      substr($hash, 0, 8), substr($hash, 8, 4), substr($hash, 12, 4),
      substr($hash, 16, 4), substr($hash, 20, 12));
  }

  public function getParameters()
  {
    $parameters = parent::getParameters();
    if ($this->getView())
    {
      $parameters['view'] = $this->getView();
    }

    return $parameters;
  }

  /**
   * Добавляет сортировку по цене
   *
   * @param Criteria $criteria
   * @param String $order Порядок сортировки: asc|desc
   */
  public function addOrderByPrice(Criteria $criteria, $order)
  {
    $colname = BasePeer::translateFieldname('Product', 'base_price', BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_COLNAME);

    $method = sprintf('add%sendingOrderByColumn', ucfirst($order));

    call_user_func(array($criteria, $method), $colname);
  }

  /**
   * Добавляет критерий сортировки по наличию
   *
   * @param Criteria $criteria
   * @param String $order Порядок сортировки: asc|desc
   */
  public function addOrderByAvailability(Criteria $criteria, $order)
  {
    if (sfContext::hasInstance())
    {
      $cityId = sfContext::getInstance()->getUser()->getLocation()->getServingCity()->getId();

      $query = ProductQuery::create()
        ->useProductAvailableStatusQuery('AvailabilityForSort', Criteria::LEFT_JOIN)
        ->orderByAvailabilityFactor($order)
        ->endUse()
        ->addJoinCondition('AvailabilityForSort', 'AvailabilityForSort.CityId = ?', $cityId);

      $criteria->mergeWith($query);
    }
  }

  public function addOrderByRank(Criteria $criteria, $order)
  {
    $query = ProductQuery::create()->orderByRank($order);

    $criteria->mergeWith($query);
  }

  /**
   * Возвращает URI для маршрутизатора Symfony
   *
   * По умолчанию переданные параметры запроса будут добавлены к заданным параметрам фильтра.
   * Если параметр с таким названием существует, его значение будет заменено переданным в массиве $parameters. Если установить флаг
   * $forceParameterSet в true, то будут использованы только те параметры, которые были переданы в $parameters. Это может понадобиться,
   * если например Вы решили не передавать один или несколько заданных параметров фильтра
   *
   * @param String $route            Маршрут Symfony
   * @param Array  $parameters       Параметры запроса
   * @param boolean $forceParameterSet
   * @return String
   */
  public function getInternalUri($route, $parameters=array(), $forceParameterSet=false)
  {
    $template = strpos($route, "?") === false ? '%s?%s' : '%s&%s';

    if (!$forceParameterSet)
    {
      $parameters = array_merge($this->getParameters(), $parameters);
    }

    return sprintf($template, $route, http_build_query($parameters));
  }

//  /**
//   *
//   * @param type $primaryOrder
//   * @return string
//   */
//  public function getNextSortForAvailability($primaryOrder)
//  {
//    if ('availability' == $this->getSortColumn() && $this->getSortOrder() == 'desc')
//    {
//      return 'none';
//    }
//
//    return 'desc';
//  }

  /**
   * Возвращает список столбцов, по которым может производиться сортировка
   *
   * @return Array
   */
  public function getSortColumns()
  {
    return $this->sortColumns;
  }

  /**
   * Задает список столбцов, по которым может производиться сортировка
   *
   * @param Array $v
   * @throws InvalidArgumentException
   */
  public function setSortColumns($v)
  {
    if (!is_array($v))
    {
      throw new InvalidArgumentException('Value must be an associative array');
    }

    $this->sortColumns = $v;
  }

  public function getGridSize()
  {
    if ($this->taxon)
    {
      $catalogSection = $this->taxon;
      /*
       * Если для этого раздела выбрано представление, подразумевающее возможность выбора способа представления,
       * т.е. любое представление, наследующее от представления "Список товаров с фильтром", то
       * значение вида отображения по умолчанию нужно взять из него. В противном случае будем использовать глобальные настройки
       * отображения
       */
      $presentationProvider = new CatalogSectionPresentationProvider();
      try
      {
        $presentation = $catalogSection->getPresentation($presentationProvider);
      }
      catch (PresentationNotFoundException $e)
      {
        $presentation = $presentationProvider->getDefaultPresentation($catalogSection);
      }

      if ($presentation instanceof CatalogSectionPresentationProductList)
      {
        $presentationOptions = $catalogSection->getCatalogSectionPresentationOptions($presentation);

        if (isset($presentationOptions['grid_size']) && $presentationOptions['grid_size'] == 'large')
        {
          return 'large';
        }
      }
    }

    return '';
  }

  public function getTableSize()
  {
    if ($this->taxon)
    {
      $catalogSection = $this->taxon;
      /*
       * Если для этого раздела выбрано представление, подразумевающее возможность выбора способа представления,
       * т.е. любое представление, наследующее от представления "Список товаров с фильтром", то
       * значение вида отображения по умолчанию нужно взять из него. В противном случае будем использовать глобальные настройки
       * отображения
       */
      $presentationProvider = new CatalogSectionPresentationProvider();
      try
      {
        $presentation = $catalogSection->getPresentation($presentationProvider);
      }
      catch (PresentationNotFoundException $e)
      {
        $presentation = $presentationProvider->getDefaultPresentation($catalogSection);
      }

      if ($presentation instanceof CatalogSectionPresentationProductList)
      {
        $presentationOptions = $catalogSection->getCatalogSectionPresentationOptions($presentation);

        if (isset($presentationOptions['table_size']) && $presentationOptions['table_size'] == 'collapsed')
        {
          return 'collapsed';
        }
      }
    }

    return '';
  }

  /**
   * Устанавливает список допустимых вариантов количества товаров на странице
   *
   * @param int[] $v
   */
  public function setMaxPerPageChoices($v)
  {
    $this->maxPerPageChoices = $v;
  }

  /**
   * Возвращает список допустимых вариантов количества товаров на странице
   *
   * @return int[]
   */
  public function getMaxPerPageChoices()
  {
    return $this->maxPerPageChoices;
  }

  protected function createQueryBuilder()
  {
    return $this->taxon->getProductQueryBuilder();
  }

  /**
   * @return TaxonInterface
   */
  public function getTaxon()
  {
    return $this->taxon;
  }

  public function apply()
  {
    $queryBuilder = parent::apply();

    //$this->eavFilter->apply($queryBuilder);

    return $queryBuilder;
  }
}