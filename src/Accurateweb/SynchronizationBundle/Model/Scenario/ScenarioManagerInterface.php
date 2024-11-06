<?php

namespace Accurateweb\SynchronizationBundle\Model\Scenario;


interface ScenarioManagerInterface
{
  /**
   * @param $alias string
   * @return SynchronizationScenarioInterface
   */
  public function get($alias);
}