<?php

namespace Accurateweb\SettingBundle\Model\Storage;

interface SettingStorageInterface
{
  public function get($name);

  public function set($name, $value);
}