<?php

namespace Accurateweb\SynchronizationBundle\Event;


use Accurateweb\SynchronizationBundle\Model\Handler\TransferHandlerInterface;
use Symfony\Component\EventDispatcher\Event;

class TransferHandlerEvent extends Event
{
  private $handler;

  public function __construct (TransferHandlerInterface $handler)
  {
    $this->handler = $handler;
  }

  /**
   * @return TransferHandlerInterface
   */
  public function getHandler ()
  {
    return $this->handler;
  }
}