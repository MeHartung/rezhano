<?php

namespace Accurateweb\SettingBundle\Model\Storage;

class PropelSettingStorage implements SettingStorageInterface
{
  private $class;

  public function __construct ($class)
  {
    $this->class = $class;
  }

  public function get ($name)
  {
    $query = sprintf('%sQuery', $this->class);
    $setting = $query::create()->filterById($name)->findOneOrCreate();

    if ($setting->isNew())
    {
      $setting->setName($name);
      $setting->save();
      return null;
    }

    return $setting->getValue();
  }

  public function set ($name, $value)
  {
    $query = sprintf('%sQuery', $this->class);
    $setting = $query::create()->filterById($name)->findOneOrCreate();

    if ($setting->isNew())
    {
      $setting->setName($name);
      $setting->save();
    }

    $setting->setValue($value);
    $setting->save();
  }
}