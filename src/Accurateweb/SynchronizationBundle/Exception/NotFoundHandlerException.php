<?php

namespace Accurateweb\SynchronizationBundle\Exception;

class NotFoundHandlerException extends \Exception
{
  public function __construct(string $name = "")
  {
    $message = sprintf("Not found transfer with name %s", $name);
    parent::__construct($message);
  }
}