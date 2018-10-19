<?php

namespace Accurateweb\SettingBundle\Model\Setting;

use Accurateweb\SettingBundle\Model\Storage\SettingStorageInterface;

abstract class AbstractChoiceSetting implements SettingInterface
{
  private $name;
  private $description;
  private $default;
  private $settingStorage;

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

    return $value;
  }

  public function setValue ($value)
  {
    $this->settingStorage->set($this->name, $value);
  }

  public function getFormType ()
  {
    return 'choice';
  }

  public function getFormOptions ()
  {
    return array(
      'choices' => $this->getChoices()
    );
  }

  /** @return array */
  protected abstract function getChoices();

  public function getStringValue ()
  {
    $choices = $this->getChoices();
    return $choices[$this->getValue()];
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