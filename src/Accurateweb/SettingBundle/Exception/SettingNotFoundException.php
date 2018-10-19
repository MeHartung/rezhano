<?php

namespace Accurateweb\SettingBundle\Exception;

class SettingNotFoundException extends \Exception
{
  public function __construct ($name)
  {
    parent::__construct(sprintf('Setting %s not found', $name), 0, null);
  }
}