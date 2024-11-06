<?php

namespace Accurateweb\SynchronizationBundle\Model;

//require_once dirname(__FILE__).'/logger/asSynchronizationLogger.class.php';

use Accurateweb\SynchronizationBundle\Model\Configuration\SynchronizationServiceConfiguration;
use Accurateweb\SynchronizationBundle\Model\Datasource\Base\BaseDataSource;
use Accurateweb\SynchronizationBundle\Model\Subject\SynchronizationSubjectInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class SynchronizationService
{
  private $configuration;
  private $dispatcher;
  private $em;
  private $logger;
  private $mode;
  
  /**
   * SynchronizationService constructor.
   *
   * @param SynchronizationServiceConfiguration $configuration
   * @param $dispatcher
   * @param EntityManagerInterface|null $em
   */
  public function __construct($mode, SynchronizationServiceConfiguration $configuration, $dispatcher, EntityManagerInterface $em = null)
  {
    $this->mode = $mode;
    $this->configuration = $configuration;
    $this->dispatcher = $dispatcher;
    $this->em = $em;
  }
  
  public function pull(SynchronizationSubjectInterface $subject, $options = array())
  {
    $filename = $this->getRemoteDataFile($subject, $options);
    
    return $this->getEngine(isset($options['mode']) ? $options['mode'] : SynchronizationMode::FULL)
      ->execute($subject, SynchronizationDirection::INCOMING, $filename, $options);
    
  }
  
  /**
   * @param $subject SynchronizationSubjectInterface
   * @param array $options
   * @throws \Exception
   */
  public function push(SynchronizationSubjectInterface $subject, $options = array())
  {
    $datasource = $this->configuration->getDatasource();
    
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
    } else
    {
      if (isset($names[$direction]))
      {
        $name = $names[$direction];
      }
    }
    
    return $name;
  }
  
  public function getRemoteDataFile(SynchronizationSubjectInterface $subject, $options = array())
  {
    $datasource = $this->configuration->getDatasource();
    
    $remoteFilename = $this->getRemoteFilename($subject, $options);
    
    $workingDir = $this->configuration->getWorkingDirectory() . '/incoming';
    
    if ((!is_dir($workingDir) && !@mkdir($workingDir, 0777, true)))
    {
      throw new \Exception('Unable to save temporary XML data. Directory "%s" doesn\'t exist and couldn\'t be created', $workingDir);
    }
    $localFilename = sprintf('%s/%s_%s.json', $workingDir, $subject->getName(), date('Ymd'));
    
    
    if (is_file($localFilename))
    {
      $i = 1;
      
      while (is_file($localFilename))
      {
        $localFilename = sprintf('%s/%s_%s_%d.json', $workingDir, $subject->getName(), date('Ymd'), $i++);
      }
    }
    
    return $datasource->get($remoteFilename, $localFilename, $this->em);
  }
  
  /**
   * @param $mode
   * @return Engine\FullSynchronizationEngine|Engine\IncrementalSynchronizationEngine
   * @throws \Exception
   */
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
