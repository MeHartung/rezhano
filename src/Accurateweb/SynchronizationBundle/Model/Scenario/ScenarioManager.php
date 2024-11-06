<?php

namespace Accurateweb\SynchronizationBundle\Model\Scenario;


use Accurateweb\SynchronizationBundle\Exception\NotFoundScenarioException;

class ScenarioManager implements ScenarioManagerInterface
{
  /** @var SynchronizationScenarioInterface[] */
  private $scenariosPool;
  
  public function __construct ()
  {
    $this->scenariosPool = array();
  }
  
  /**
   * @param $alias
   * @return SynchronizationScenarioInterface
   */
  public function get($alias)
  {
    return $this->scenariosPool[$alias];
  }
  
  public function getAll()
  {
    return $this->scenariosPool;
  }
  
  public function addScenario (SynchronizationScenarioInterface $scenario)
  {
    $alias = $scenario->getName();
    $this->scenariosPool[$alias] = $scenario;
  }
}