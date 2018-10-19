<?php

namespace Accurateweb\SettingBundle\Model\Setting;

use Accurateweb\SettingBundle\Model\Storage\SettingStorageInterface;

class ChoiceSetting extends AbstractChoiceSetting
{
  private $choices;

  public function __construct (SettingStorageInterface $settingStorage, $name, $description, $default, $choices)
  {
    $this->choices = $choices;
    parent::__construct($settingStorage, $name, $description, $default);
  }

  protected function getChoices ()
  {
    return $this->choices;
  }
}