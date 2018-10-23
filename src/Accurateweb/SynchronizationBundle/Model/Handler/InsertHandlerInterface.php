<?php

namespace Accurateweb\SynchronizationBundle\Model\Handler;

interface InsertHandlerInterface extends HandlerInterface
{
  public function insert(string $query);
}