<?php

namespace Accurateweb\SynchronizationBundle\Model\Handler;

use Accurateweb\SynchronizationBundle\Exception\NotFoundHandlerException;

class SynchronizationHandlerManager implements SynchronizationHandlerManagerInterface
{
  private $insertTrnasfersPool = [];
  private $handlerTrnasfersPool = [];
  
  /**
   * @param string $name
   * @return TransferHandlerInterface|mixed
   * @throws NotFoundHandlerException
   */
  public function getTransferHandler($name)
  {
    if($result = $this->handlerTrnasfersPool[$name])
    {
      throw new NotFoundHandlerException($name);
    }
    return $result;
  }
  
  /**
   * @param string $name
   * @return InsertHandlerInterface
   * @throws NotFoundHandlerException
   */
  public function getInsertHandler($name)
  {
    if($result = $this->handlerTrnasfersPool[$name])
    {
      throw new NotFoundHandlerException($name);
    }
    return $result;
  }
  
  /**
   * @param InsertHandlerInterface $insertHandler
   */
  public function addInsertHandler(InsertHandlerInterface $insertHandler)
  {
    $this->insertTrnasfersPool[$insertHandler->getName()] = $insertHandler;
  }
  
  public function addTransferHandler(TransferHandlerInterface $transferHandler)
  {
    $this->handlerTrnasfersPool[$transferHandler->getName()] = $transferHandler;
  
  }
}