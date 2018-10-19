<?php

namespace Accurateweb\SettingBundle\Model\Setting;

class NumericSetting extends StringSetting
{
  public function getValue ()
  {
    $value = $this->settingStorage->get($this->name);

    if (is_null($value))
    {
      return $this->default;
    }

    return (float)$value;
  }

  public function setValue ($value)
  {
    $this->settingStorage->set($this->name, (float)$value);
  }

  public function getFormType ()
  {
    return 'Symfony\Component\Form\Extension\Core\Type\NumberType';
  }
}