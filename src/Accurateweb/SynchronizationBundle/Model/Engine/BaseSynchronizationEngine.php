<?php

namespace Accurateweb\SynchronizationBundle\Model\Engine;

use Accurateweb\SynchronizationBundle\Model\Configuration\SynchronizationServiceConfiguration;

abstract class BaseSynchronizationEngine implements SynchronizationEngineInterface
{
  /**
   * @var SynchronizationServiceConfiguration
   */
  protected $configuration;

  public function __construct($configuration)
  {
    $this->configuration = $configuration;
  }

  abstract public function execute($subject, $direction, $local_filename, $options=array());
}
