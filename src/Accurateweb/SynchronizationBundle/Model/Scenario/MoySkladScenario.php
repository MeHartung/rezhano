<?php

namespace Accurateweb\SynchronizationBundle\Model\Scenario;

use Accurateweb\SynchronizationBundle\Model\SynchronizationScenario;
#use AppBundle\Entity\Event\OrderEvent;
#use AppBundle\EventListener\Synchronization\MoySklad\MoySkladSyncOrderEvent;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class MoySkladScenario extends SynchronizationScenario
{
  private $em, $dumpResult = true, $logger;
  protected $dispatcher;
  
  
  public function __construct(?EventDispatcherInterface $dispatcher = null, string $name = '', $subjects,
                              EntityManagerInterface $em, LoggerInterface $logger)
  {
    parent::__construct($dispatcher, $name);
    
    foreach ($subjects as $subject)
    {
      $this->addSubject($subject);
    }
    
    $this->dispatcher = $dispatcher;
    $this->em = $em;
    $this->logger = $logger;
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
  /*  $orderEvents = $this->em->getRepository(OrderEvent::class)->findBy(['updatedAt' => null]);
    
    foreach ($orderEvents as $event)
    {
      try
      {
        $data = json_decode($event->getData(), true);
        $this->dispatcher->dispatch($event->getEventName(), new MoySkladSyncOrderEvent($data));
        $event->setUpdatedAt(new \DateTime());
        
        $this->em->persist($event);
        $this->em->flush();
      }catch (\Exception $exception)
      {
        $this->logger->error($exception->getMessage() . $exception->getTraceAsString());
      }
    }*/
  }
}