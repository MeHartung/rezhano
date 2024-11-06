<?php

namespace Accurateweb\SynchronizationBundle\Event;


use Accurateweb\SynchronizationBundle\Model\Scenario\SynchronizationScenarioInterface;
use Accurateweb\SynchronizationBundle\Model\Subject\SynchronizationSubjectInterface;

class SynchronizationScenarionSubjectEvent extends SynchronizationScenarioEvent
{
  private $subject;

  public function __construct (SynchronizationScenarioInterface $scenario, SynchronizationSubjectInterface $subject)
  {
    $this->subject = $subject;
    parent::__construct($scenario);
  }

  /**
   * @return SynchronizationSubjectInterface
   */
  public function getSubject ()
  {
    return $this->subject;
  }
}