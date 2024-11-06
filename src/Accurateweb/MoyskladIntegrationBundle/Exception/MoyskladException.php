<?php

namespace Accurateweb\MoyskladIntegrationBundle\Exception;

class MoyskladException extends \Exception
{
  private $info;

  public function __construct ($message = "", $code = 0, $info=null)
  {
    $this->info = $info;
    parent::__construct($message, $code, null);
  }

  /**
   * @return null
   */
  public function getInfo ()
  {
    return $this->info;
  }
}