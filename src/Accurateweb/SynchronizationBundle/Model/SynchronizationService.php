<?php

namespace Accurateweb\SynchronizationBundle\Model;

//require_once dirname(__FILE__).'/logger/asSynchronizationLogger.class.php';

use Accurateweb\SynchronizationBundle\Model\Configuration\SynchronizationServiceConfiguration;
use Accurateweb\SynchronizationBundle\Model\Datasource\Base\BaseDataSource;
use Accurateweb\SynchronizationBundle\Model\Enginge\FullSynchronizationEngine;
use Accurateweb\SynchronizationBundle\Model\Enginge\IncrementalSynchronizationEngine;
use Psr\Log\InvalidArgumentException;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Security\Acl\Exception\Exception;

class SynchronizationService
{
  private $configuration;
  private $dispatcher;
  private $logger;

  public function __construct($configuration, $dispatcher)
  {
    if (!( $configuration instanceof SynchronizationServiceConfiguration ))
    {
      throw new InvalidArgumentException('$configuration must be an instance of SynchronizationServiceConfiguration');
    }

    $this->configuration = $configuration;
    $this->dispatcher = $dispatcher;
  //  $this->logger = asSynchronizationLogger::createInstance($this, $dispatcher);
  }

  public function pull($subject, $options = array())
  {
    $filename = $this->getRemoteDataFile($subject, $options);
   // $filename = "/home/evgeny/dev/web/synchronization/incoming/catalog.xml";

    return $this->getEngine(isset($options['mode']) ? $options['mode'] : SynchronizationMode::FULL)
                ->execute($subject, SynchronizationDirection::INCOMING, $filename, $options);
  }

  private function combineValue($name, $subject, $options)
  {
    return isset($options[$name]) ? $options[$name] : $this->configuration->getDefaultOf($subject, $name);
  }

  public function push($subject, $options = array())
  {
    $datasource = $this->getDatasource($subject, $options);
    $remoteFilename = $this->getRemoteFilename($subject, $options, 'outgoing');
    $workingDir = $this->configuration->getWorkingDirectory() . '/outgoing';

    if ((!is_dir($workingDir) && !@mkdir($workingDir, 0777, true)))
    {
      throw new \Exception('Unable to save temporary XML data. Directory "%s" doesn\'t exist and couldn\'t be created', $behafigeji);
    }

    $localFilename = $workingDir . '/' . $subject . '.xml';
    $parser = $this->configuration->getParser($subject);

    if (!$parser)
    {
      throw new \Exception('Unable to instantiate parser class');
    }

    $objects = $parser->fetchObjects();
    file_put_contents($localFilename, $parser->serialize($objects));
    $datasource->put($localFilename, $remoteFilename);
  }

  protected function getDatasource($subject, $options)
  {
    $datasource = $this->combineValue("datasource", $subject, $options);

    if (!$datasource instanceof BaseDataSource && is_string($datasource))
    {
      $datasource = $this->configuration->getDatasource($datasource);
    }
    
    if (!$datasource)
    {
      throw new InvalidConfigurationException('Unable to create datasource.');
    }

    return $datasource;
  }

  protected function getRemoteFilename($subject, $options, $direction = SynchronizationDirection::INCOMING)
  {

    if (isset($options['filename']))
    {
      return $options['filename'];
    }
    $names = $this->configuration->getDefaultOf($subject, 'filename');
    $name = null;

    if (is_string($names))
    {
      $name = $names;
    }
    else
    {
      if (isset($names[$direction]))
      {
        $name = $names[$direction];
      }
    }

    return $name;
  }

  public function getRemoteDataFile($subject, $options = array())
  {
    $datasource = $this->getDatasource($subject, $options);
    $remoteFilename = $this->getRemoteFilename($subject, $options);
    $workingDir = $this->configuration->getWorkingDirectory() . '/incoming';

    if ((!is_dir($workingDir) && !@mkdir($workingDir, 0777, true)))
    {
      throw new \Exception('Unable to save temporary XML data. Directory "%s" doesn\'t exist and couldn\'t be created', $workingDir);
    }
    $localFilename = sprintf('%s/%s_%s.xml', $workingDir, $subject, date('Ymd'));


    if (is_file($localFilename))
    {
      $i = 1;

      while (is_file($localFilename))
      {
        $localFilename = sprintf('%s/%s_%s_%d.xml', $workingDir, $subject, date('Ymd'), $i++);
      }
    }
    
    return $datasource->get($remoteFilename, $localFilename);
  }

  public function getEngine($mode)
  {
    switch ($mode)
    {
      case SynchronizationMode::FULL:
        {
          return new \Accurateweb\SynchronizationBundle\Model\Engine\FullSynchronizationEngine($this->configuration);
        }
      case SynchronizationMode::INCREMENTAL:
        {
          return new \Accurateweb\SynchronizationBundle\Model\Engine\IncrementalSynchronizationEngine($this->configuration);
        }
      default:
        {
          throw new \Exception(sprintf('Unsupported mode: \'%s\'', $mode));
        }
    }
  }

}
