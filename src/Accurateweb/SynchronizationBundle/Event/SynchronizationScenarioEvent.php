<?php

namespace Accurateweb\SynchronizationBundle\Event;

use Accurateweb\SynchronizationBundle\Model\Scenario\SynchronizationScenarioInterface;
use Symfony\Component\EventDispatcher\Event;

class SynchronizationScenarioEvent extends Event
{
  private $scenario;

  public function __construct (SynchronizationScenarioInterface $scenario)
  {
    $this->scenario = $scenario;
  }

  /**
   * @return SynchronizationScenarioInterface
   */
  public function getScenario ()
  {
    return $this->scenario;
  }
}