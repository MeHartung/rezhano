<?php

namespace Accurateweb\SynchronizationBundle\Exception;

class NotFoundScenarioException extends \Exception
{
  public function __construct ($name)
  {
    parent::__construct(sprintf('Scenario %s not found', $name), 0, null);
  }
}