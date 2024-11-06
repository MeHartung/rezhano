<?php

namespace Accurateweb\SettingBundle\Model\Setting;

use Accurateweb\SettingBundle\Model\Storage\SettingStorageInterface;
use Symfony\Component\Form\CallbackTransformer;

class BooleanSetting implements SettingInterface
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

    if ($value === 'false')
    {
      return false;
    }

    return (boolean)$value;
  }

  public function setValue ($value)
  {
    $value = (boolean)$value;

    if ($value)
    {
      $value = 'true';
    }
    else
    {
      $value = 'false';
    }

    $this->settingStorage->set($this->name, $value);
  }

  public function getFormType ()
  {
    return 'choice';
  }

  public function getFormOptions ()
  {
    return array(
      'choices' => array(
        'true' => 'Да',
        'false' => 'Нет'
      )
    );
  }

  public function getStringValue ()
  {
    return $this->getValue()?'Да':'Нет';
  }

  public function getDescription ()
  {
    return $this->description;
  }

  public function getModelTransformer()
  {
    return new CallbackTransformer([$this, 'transform'], [$this, 'reverseTransform']);
  }

  public function transform($value)
  {
    return $value;
  }

  public function reverseTransform($value)
  {
    if (is_string($value))
    {
      if ($value === 'true')
      {
        $value = true;
      }
      else
      {
        $value = false;
      }
    }

    return $value;
  }
}