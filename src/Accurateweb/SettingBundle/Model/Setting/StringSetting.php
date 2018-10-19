<?php

namespace Accurateweb\SettingBundle\Model\Setting;

use Accurateweb\SettingBundle\Model\Storage\SettingStorageInterface;

class StringSetting implements SettingInterface
{
  protected $name;
  protected $description;
  protected $default;
  protected $settingStorage;

  public function __construct (SettingStorageInterface $settingStorage, $name, $description, $default)
  {
    $this->settingStorage = $settingStorage;
    $this->name = $name;
    $this->description = $description;
    $this->default = $default;
  }

  public function getName ()
  {
    return $this->name;
  }

  public function getValue ()
  {
    $value = $this->settingStorage->get($this->name);

    if (is_null($value))
    {
      return $this->default;
    }

    return (string)$value;
  }

  public function setValue ($value)
  {
    $this->settingStorage->set($this->name, (string)$value);
  }

  public function getFormType ()
  {
    return 'Symfony\Component\Form\Extension\Core\Type\TextType';
  }

  public function getFormOptions ()
  {
    return array();
  }

  public function getStringValue ()
  {
    return (string)$this->getValue();
  }

  public function getDescription ()
  {
    return $this->description;
  }

  public function getModelTransformer()
  {
    return null;
  }
}