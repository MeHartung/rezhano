<?php

namespace Accurateweb\SettingBundle\Model\Manager;

use Accurateweb\SettingBundle\Exception\SettingNotFoundException;
use Accurateweb\SettingBundle\Model\Setting\SettingInterface;

class SettingManager implements SettingManagerInterface
{
  private $settings_pool;

  public function __construct ()
  {
    $this->settings_pool = array();
  }

  public function getValue($name)
  {
    if (!isset($this->settings_pool[$name]))
    {
      return null;
//      throw new SettingNotFoundException($name);
    }

    return $this->settings_pool[$name]->getValue();
  }

  public function setValue($name, $value)
  {
    if (!isset($this->settings_pool[$name]))
    {
      throw new SettingNotFoundException($name);
    }

    $this->settings_pool[$name]->setValue($value);
    return $this;
  }

  public function getSetting($alias)
  {
    if (!isset($this->settings_pool[$alias]))
    {
      throw new SettingNotFoundException($alias);
    }

    return $this->settings_pool[$alias];
  }

  public function addSetting (SettingInterface $setting)
  {
    $alias = $setting->getName();
    $this->settings_pool[$alias] = $setting;
  }

  public function getSettings()
  {
    return $this->settings_pool;
  }
}