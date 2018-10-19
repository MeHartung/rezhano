<?php

namespace Accurateweb\SettingBundle\Model\Setting;


use Accurateweb\SettingBundle\Model\Storage\SettingStorageInterface;

class LocalAdminUserSetting extends AbstractChoiceSetting
{
  const FORBIDDEN = 'none';
  const EDIT = 'edit';
  const CREATE_AND_EDIT = 'create_and_edit';

  public function __construct (SettingStorageInterface $settingStorage, $name, $description)
  {
    $default = self::FORBIDDEN;
    parent::__construct($settingStorage, $name, $description, $default);
  }

  protected function getChoices ()
  {
    return [
      self::FORBIDDEN => 'Недоступно',
      self::EDIT => 'Только изменение существующих',
      self::CREATE_AND_EDIT => 'Создание новых и изменение существующих',
    ];
  }
}