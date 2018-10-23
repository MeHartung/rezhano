<?php

namespace Accurateweb\SynchronizationBundle\Model\Handler;

interface SynchronizationHandlerManagerInterface
{
  /**
   * @param $name string
   * @return TransferHandlerInterface
   */
  public function getTransferHandler($name);
  
  /**
   * @param $name string
   * @return InsertHandlerInterface
   */
  public function getInsertHandler($name);
  
  /**
   * @param InsertHandlerInterface $transferHandler
   * @return void
   */
  public function addInsertHandler(InsertHandlerInterface $transferHandler);
  
  /**
   * @param TransferHandlerInterface $insertHandler
   * @return void
   */
  public function addTransferHandler(TransferHandlerInterface $insertHandler);
}