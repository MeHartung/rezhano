<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace AccurateCommerce\Store\Catalog\Filter;

use Accurateweb\FilteringBundle\Form\Type\FilterType;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use StoreBundle\Repository\Store\Catalog\Product\ProductRepository;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;


/**
 * Базовый класс фильтра
 *
 * @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
abstract class BaseFilter
{
//  const DISPLAY_GENERIC = 0;
//  const DISPLAY_EDIT = 1;

//  static private $widgetMap=null;

  private $id;

//  /**
//   * @var asBaseFilterFieldSchema
//   */
//  protected $fieldSchema;
//
//  /** @var asWidgetFactory */
//  protected $widgetFactory;
//
//  /** @var asWidgetMap */
//  //protected $widgetMap;
//
//  /** @var IFieldConfigurationProvider */
//  private $fieldConfigurationProvider;
//
//  /** @var IFilterConfiguration */
//  private $configuration;
//
//  protected $displayMode;
//
//  private $valuesActualized;

  private $query;

  private $maxPerPage;

  private $page = 1;

  /**
   * @var array
   */
  private $fields;

//  private $persistConfiguration = true;
//
//  public function getDisplayMode()
//  {
//    return $this->displayMode;
//  }
//
//  /**
//   * Устанавливает режим отображения фильтра
//   *
//   * Функция позволяет включить или отключить режим редактирования для текущего фильтра.
//   * Чтобы включить режим редактирования фильтра, установите значение $v равным DISPLAY_EDIT
//   *
//   * @param int $v Одна из констант DISPLAY_
//   */
//  public function setDisplayMode($v)
//  {
//    $this->displayMode = $v;
//
//    $this->fieldSchema->forceEnableFields($this->displayMode == self::DISPLAY_EDIT);
//
//    $this->configureWidgets();
//    $this->updateWidgets();
//  }

  public function __construct($id)
  {
    $this->id = $id;
    $this->fields = array();

//    $this->valuesActualized = false;
//    $this->fieldSchema = new asBaseFilterFieldSchema();
//
//    $configurationProvider = new asPropelFilterConfigurationProvider();
//    $this->configuration = $configurationProvider->getFilterConfiguration($id);
//
//    $this->widgetFactory = new asWidgetFactory(self::getWidgetMap());
//
//    $this->displayMode = self::DISPLAY_GENERIC;
//
//    $this->saveConfiguration();
  }

//// <editor-fold defaultstate="collapsed" desc="Serializable">
//
//  /**
//   * Выполняет десериализацию из массива значений
//   *
//   * @param Array $values
//   * @return boolean
//   */
//  public function fromArray($values)
//  {
//    if (isset($values["fieldSchema"]))
//      $this->fieldSchema->fromArray($values["fieldSchema"]);
//
//    $this->setup();
//  }
//
//
//  /**
//   * Выполняет десериализацию из строки
//   *
//   * @param string $serialized
//   * @see Serializable::unserialize
//   */
//  public function unserialize($serialized)
//  {
//    $values = unserialize($serialized);
//    $this->fromArray($values);
//  }
//
//
//  /**
//   *
//   * @return Array
//   */
//  protected function toArray()
//  {
//    return array("fieldSchema" => $this->fieldSchema->toArray());
//  }
//
//  /**
//   * Выполняет сериализацию объекта в строку
//   *
//   * @return string
//   */
//  public function serialize()
//  {
//    return serialize($this->doSerialize());
//  }
//
//// </editor-fold>

  /**
   * @param FormFactoryInterface $formFactory
   * @return \Symfony\Component\Form\FormInterface
   */
  public function createForm(FormFactoryInterface $formFactory, array $options = array())
  {
    foreach ($this->fields as $key => $field)
    {
      $queryBuilder = $this->createQuerybuilder();

      $this->buildQuery($queryBuilder);

      $field->setup($queryBuilder);
    }

    $formBuilder = $formFactory->createNamedBuilder('f', FilterType::class, null,
      array_merge($options, array(
        'csrf_protection' => false,
        'filter' => $this
      )));

    $formBuilder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onFormPreSetData'));
    $formBuilder->addEventListener(FormEvents::POST_SUBMIT, array($this, 'onFormPostSubmit'));

    foreach ($this->fields as $field)
    {
      $field->buildForm($formBuilder);
    }

    return $formBuilder->getForm();
  }

//  public function configureWidgets()
//  {
//    $widgets = $this->fieldSchema->createWidgets($this->widgetFactory, $this->configuration, $this->displayMode == self::DISPLAY_EDIT);
//    $validators = $this->fieldSchema->createValidators();
//
//    $this->setWidgets($widgets);
//    $this->setValidators($validators);
//
//    $this->widgetSchema->setNameFormat("f[%s]");
//  }

//  public function setFields($fields)
//  {
//    $this->fieldSchema->setFields($fields);
//
//    $this->configureFields($fields);
//    $this->configureWidgets();
//
//    $this->saveConfiguration();
//  }
//
//
//  public function setField($key, $field)
//  {
//    $this->fieldSchema->setField($key, $field);
//
//    $this->updateFieldConfiguration($field);
//    $this->configureWidgets();
//
//    $this->widgetSchema->setNameFormat("f[%s]");
//
//    try
//    {
//      $this->configuration->save();
//    }
//    catch (Exception $e)
//    {
//      if (sfContext::hasInstance())
//      {
//        sfContext::getInstance()->getLogger()->err('Unable to save filter configuration: '.(string)$e);
//      }
//    }
//  }

//  private function updateFieldConfiguration($field)
//  {
//    if (!$field instanceof asBaseFilterFieldSchema)
//      $this->configuration->addField($field);
//    else
//      $this->configureFields($field);
//  }
//
//
//  private function configureFields($fields)
//  {
//    foreach ($fields as $field)
//    {
//      $this->updateFieldConfiguration($field);
//    }
//
//  }

//  public function setFieldConfigurationProvider(IFieldConfigurationProvider $provider)
//  {
//    $this->fieldConfigurationProvider = $provider;
//  }
//
//  public function getFieldConfigurationProvider()
//  {
//    return $this->fieldConfigurationProvider;
//  }
//
//  public function getAvailableWidgets($cls)
//  {
//    return $this->widgetFactory->getWidgetNames($cls);
//  }
//
//  public function getField($name)
//  {
//    return $this->fieldSchema->findRecursive($name);
//  }

  public function getId()
  {
    return $this->id;
  }

//  /**
//   *
//   * @return asWidgetClassMap
//   */
//  static public function getWidgetMap()
//  {
//    if (!self::$widgetMap)
//    {
//      self::$widgetMap = asWidgetClassMapProvider::getInstance()->getWidgetClassMap();
//    }
//
//    return self::$widgetMap;
//  }

  /**
   * Возвращает критерий выборки. Применяет фильтр, если не применен
   *
   * @param Array $options
   * @return Criteria
   */
  public function getQuery()
  {
    if (!$this->query)
    {
      $this->apply();
    }

    return $this->query;
  }

  protected function buildQuery(QueryBuilder $queryBuilder)
  {
    foreach ($this->fields as $field)
    {
      /**
       * @var $field FilterField
       */
      $field->apply($queryBuilder);
    }

    //$this->addOrderByColumns($queryBuilder);
  }

//  public function refreshValues()
//  {
//    $this->fieldSchema->refreshValues(null);
//
//    $this->updateWidgets();
//    $this->updateValues();
//
//    $this->valuesActualized = true;
//  }
//
//  public function enableValues()
//  {
//    //Это скорее всего некорректное поведение, но поскольку как всегда все надо было сделать еще позавчера,
//    //делаем через жопу
//    $this->fieldSchema->enableValues(null);
//
//    $this->updateWidgets();
//    $this->updateValues();
//
//    $this->valuesActualized = true;
//  }
//
//  protected function updateWidgets()
//  {
//    $this->fieldSchema->updateWidgets($this->widgetSchema->getFields());
//    $this->resetFormFields();
//  }
//
//  protected function updateValidators()
//  {
//    $this->fieldSchema->updateValidators($this->widgetSchema->getFields());
//    $this->resetFormFields();
//  }
//
//  protected function updateValues()
//  {
//    $values = $this->fieldSchema->getValues();
//
//    $this->setDefaults($values);
//  }

  /**
   * Применяет фильтр
   *
   * @return QueryBuilder
   */
  public function apply()
  {
    //Критерий придется собирать два раза - первый раз, чтобы знать границы значений для всех полей фильтра
    $queryBuilder = $this->createQueryBuilder();

    $this->buildQuery($queryBuilder);
//    $this->refreshValues();
//
//    if ($this->isBound() && $this->isValid())
//    {
//      $this->fieldSchema->setValues($this->getValues());
//
//      //А второй - чтобы знать границы фильтра с заданными значениями, причем придется и фильтры пересчитывать
//    $this->buildQuery();
//    $this->enableValues();
//    }

    //$this->query = $queryBuilder->getQuery();

    //return $this->query;
    return $queryBuilder;
  }

  /**
   * Возвращает true, если фильтр был применен
   *
   * @return bool
   */
  public function getApplied()
  {
    return $this->query != null;
  }

  /**
   * Возвращает массив параметров для передачи в строку адреса браузера
   *
   * @return array
   */
  public function getRouteParameters()
  {
    $parameters = array();
    foreach ($this->fields as $key => $field)
    {
      /** @var $field FilterField */
      if ($field->getValue())
      {
        $parameters[$key] = $field->getValue();
      }
    }

    return $parameters;
  }

  public function getPage()
  {
    return $this->page;
  }

  public function setPage($v)
  {
    $this->page = (int)$v;
  }

  public function getMaxPerPage()
  {
    return $this->maxPerPage;
  }

  public function setMaxPerPage($v)
  {
    $this->maxPerPage = (int)$v;
  }

  /**
   * Добавляет в критерий выборки сортировку по столбцам
   *
   * @param Criteria $criteria
   */
  protected function addOrderByColumns($criteria)
  {
  }


//
//  public function getJavaScripts()
//  {
//    $javascripts = parent::getJavaScripts();
//
//    $javascripts[] = '/js/catalog/filter/jquery.filter-control-panel.js';
//
//    return $javascripts;
//
//  }
//
//  /**
//   * Указывает, следует ли сохранять конфигурацию полей текущего фильтра. Позволяет включать и отключать
//   * сохранение таких параметров конфигурации полей, как "Показывать свернутым", "Элемент управления" и т.д.
//   *
//   * @param boolean $v Флаг сохранения конфигурации полей фильтра
//   */
//  public function setConfigurationPersistence($v)
//  {
//    $this->persistConfiguration = (bool)$v;
//  }
//
//  /**
//   * Выполняет сохранение конфигурации полей фильтра, если сохранение конфигурации полей фильтра доступно для данного фильтра
//   *
//   * @param boolean $force Если имеет значение TRUE, конфигурация фильтра будет сохранена независимо от состояния флага
//   * сохранения конфигурации полей фильтра
//   *
//   * @return boolean
//   */
//  public function saveConfiguration($force=false)
//  {
//    if ($this->persistConfiguration || $force)
//    {
//      try
//      {
//        $this->configuration->save();
//
//        return true;
//      }
//      catch (Exception $e)
//      {
//        if (sfContext::hasInstance())
//        {
//          sfContext::getInstance()->getLogger()->err('Unable to save filter configuration: '.(string)$e);
//        }
//      }
//    }
//
//    return false;
//  }

  public function onFormPreSetData(FormEvent $event)
  {
//      $data = array();
//
//      foreach ($this->fields as $key => $field)
//      {
//        $queryBuilder = $this->createQuerybuilder();
//
//        $this->buildQuery($queryBuilder);
//
//        $data[$key] = $field->evaluate($queryBuilder);
//      }
//
//      $event->setData($data);
  }


  public function addField(FilterField $field)
  {
    $this->fields[$field->getId()] = $field;
  }

  abstract protected function createQueryBuilder();

  /**
   * @return FilterField[]
   */
  public function getFields()
  {
    return $this->fields;
  }

  public function onFormPostSubmit(FormEvent $event)
  {
    $values = $event->getData();

    foreach ($values as $key => $value)
    {
      if (isset($this->fields[$key]))
      {
        $this->fields[$key]->setValue($value);
      }
    }

    foreach ($this->fields as $key => $field)
    {
      $this->adjust($key);
    }
  }

  protected function adjust($fieldId)
  {
    $queryBuilder = $this->createQueryBuilder();

    foreach ($this->fields as $key => $field)
    {
      if ($key === $fieldId)
      {
        continue;
      }

      $field->apply($queryBuilder);
    }

    $this->fields[$fieldId]->adjust($queryBuilder);
  }
}