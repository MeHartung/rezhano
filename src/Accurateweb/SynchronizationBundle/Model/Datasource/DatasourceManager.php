<?php

namespace Accurateweb\SynchronizationBundle\Model\Datasource;

class DatasourceManager implements DatasourceManagerInterface
{
  private $datasourcesPool = [];

  /**
   * @param string $alias
   * @return DatasourceInterface|mixed
   */
  public function get($alias)
  {
   return $this->datasourcesPool[$alias];
  }
  
  /**
   * @param DatasourceInterface $datasource
   */
  public function addDatasource(DatasourceInterface $datasource)
  {
    $this->datasourcesPool[$datasource->getName()] = $datasource;
  }
}