<?php

namespace Accurateweb\SynchronizationBundle\Model\Handler;


interface HandlerInterface
{
  public function query(string $query);
}