<?php

namespace Accurateweb\SynchronizationBundle\Model\Scenario;

use Accurateweb\SynchronizationBundle\Model\Subject\SynchronizationSubjectInterface;
use Accurateweb\SynchronizationBundle\Model\SynchronizationScenario;
#use StoreBundle\Entity\Event\OrderEvent;
#use StoreBundle\EventListener\Synchronization\MoySklad\MoySkladSyncOrderEvent;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use StoreBundle\Entity\Store\Catalog\Product\ProductImage;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class MoySkladScenario extends SynchronizationScenario
{
  private $em, $dumpResult = true, $logger, $kernelRootDir;
  protected $dispatcher;
  
  
  public function __construct(?EventDispatcherInterface $dispatcher = null, string $name = '', $subjects,
                              EntityManagerInterface $em, LoggerInterface $logger, $kernelRootDir)
  {
    parent::__construct($dispatcher, $name);
    
    foreach ($subjects as $subject)
    {
      $this->addSubject($subject);
    }
    
    $this->dispatcher = $dispatcher;
    $this->em = $em;
    $this->logger = $logger;
    $this->kernelRootDir = $kernelRootDir;
  }
  
  public function preExecute()
  {
    # сделаем копию таблицы до синхронизации
    /*    try
        {
          $this->em->getConnection()->query('DROP TABLE IF EXISTS seven_seconds_orders_pre_execute_tmp;
    CREATE TEMPORARY TABLE IF NOT EXISTS seven_seconds_orders_pre_execute_tmp (`order_id` VARCHAR(255),
    `message` VARCHAR(512),
    `updated_at` VARCHAR(512),
    `is_ok` int,
    `cdek_code` VARCHAR(255),
    `delivered` int,
    `sent_notify` int);');
          $this->em->getConnection()->query('INSERT INTO seven_seconds_orders_pre_execute_tmp
     (`order_id`,
    `message`,
    `updated_at`,
    `is_ok`,
    `cdek_code`,
    `delivered`,
    `sent_notify`)
    SELECT `order_id`,
    `message`,
    `updated_at`,
    `is_ok`,
    `cdek_code`,
    `delivered`,
    `sent_notify` FROM seven_seconds_orders');
          $this->dumpResult = true;
        } catch (\Exception $exception)
        {
          $this->logger->addError('Unable to transfer data to a temporary table.');
          $this->logger->addError($exception->getMessage());
        }
        
        return true;*/
  }
  
  
  public function postExecute()
  {
    $isUseImageSubject = false;
    
    /**
     * Если была синхронизация картинок
     * @var SynchronizationSubjectInterface $subject
     */
    foreach ($this->subjects as $subject)
    {
      if($subject->getName() == 'moy_sklad_image')
      {
        $isUseImageSubject = true;
        break;
      }
    }
    
    if($isUseImageSubject === true)
    {
      $this->dispatcher->dispatch(
        'aw.sync.order_event.message',
        new GenericEvent("Try create ProductImage thumbnails, use media:thumbnails:generate command")
      );
      
      # ибо консоль в bin
      $kernel = $this->kernelRootDir . '\\..\\bin';
      $className = ProductImage::class;
      $process = new Process("php $kernel/console media:thumbnails:generate $className");
      $process->run();

// executes after the command finishes
      if (!$process->isSuccessful())
      {
        $errorText = $process->getErrorOutput();
        $errorCode = $process->getExitCode();
        $this->dispatcher->dispatch(
          'aw.sync.order_event.message',
          new GenericEvent("Can't create thumbnail. Error code: $errorCode. Text: \n $errorText")
        );
        
        $this->logger->error("Can't create thumbnail. Error code: $errorCode. Text: \n $errorText");
      }
      
      $this->dispatcher->dispatch(
        'aw.sync.order_event.message',
        new GenericEvent("Thumbnails generated!")
      );
      
    }
   
  }
}