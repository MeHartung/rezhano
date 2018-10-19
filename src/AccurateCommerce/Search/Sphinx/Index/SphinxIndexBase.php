<?php

/**
 *  @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */

namespace AccurateCommerce\Search\Sphinx\Index;

/**
 * Абстрактный класс описания индекса данных Sphinx
 */
abstract class SphinxIndexBase 
{
  private $sourceId,
          $model,
          $sphinxIndex;
  
  /**
   * Конструктор.
   * 
   * @param int $sourceId Идентификатор источника данных, как указано в файле конфигурации Sphinx
   * @param string $model Модель Propel, для которой создан индекс
   * @param string $sphinxIndex Название индекса в файле конфигурации Sphinx
   */
  public function __construct($sourceId, $model, $sphinxIndex)
  {
    $this->sourceId = $sourceId;
    $this->model = $model;
    $this->sphinxIndex = $sphinxIndex;
  } 
  
  /**
   * Возвращает идентификатор источника данных
   * 
   * @return int
   */
  public function getSourceId()
  {
    return $this->sourceId;
  }
  
  /**
   * Возвращает название модели Propel для этого индекса 
   * 
   * @return String
   */
  public function getModel()
  {
    return $this->model;
  }
  
  /**
   * Возвращает название индекса, как указано в файле конфигурации Sphinx
   * 
   * @return String
   */
  public function getSphinxIndex()
  {
    return $this->sphinxIndex;
  }
  
  /**
   * Возвращает название класса Peer для модели
   * 
   * @return String
   */
  public function getPeerClassName()
  {
    return constant($this->getModel() . "::PEER");
  }
  
}
