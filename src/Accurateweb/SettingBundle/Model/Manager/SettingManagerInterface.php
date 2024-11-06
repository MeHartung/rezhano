<?php

namespace Accurateweb\SettingBundle\Model\Manager;

use Accurateweb\SettingBundle\Model\Setting\SettingInterface;

interface SettingManagerInterface
{
  /**
   * @param $name string
   * @return mixed
   */
  public function getValue($name);

  /**
   * @param $name string
   * @param $value mixed
   * @return SettingManagerInterface
   */
  public function setValue($name, $value);

  /**
   * @return mixed
   */
  public function addSetting(SettingInterface $setting);

  /**
   * @param $alias
   * @return SettingInterface
   */
  public function getSetting($alias);

  /**
   * @return SettingInterface[]
   */
  public function getSettings();
}