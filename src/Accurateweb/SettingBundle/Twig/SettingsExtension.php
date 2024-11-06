<?php

namespace Accurateweb\SettingBundle\Twig;

use Accurateweb\SettingBundle\Exception\SettingNotFoundException;
use Accurateweb\SettingBundle\Model\Manager\SettingManagerInterface;

class SettingsExtension extends \Twig_Extension
{
  private $settingManager;

  public function getName ()
  {
    return 'settings_extension';
  }

  public function __construct (SettingManagerInterface $settingManager)
  {
    $this->settingManager = $settingManager;
  }

  public function getFunctions ()
  {
    return array(
      new \Twig_SimpleFunction('setting', array($this, 'getValue')),
      new \Twig_SimpleFunction('settingString', array($this, 'getSettingString')),
      new \Twig_SimpleFunction('settingDescription', array($this, 'getSettingDescription')),
    );
  }

  public function getValue ($name)
  {
    return $this->settingManager->getValue($name);
  }

  public function getSettingDescription($name)
  {
    try
    {
      $setting = $this->settingManager->getSetting($name);
    }
    catch (SettingNotFoundException $e)
    {
      return '';
    }
    return $setting->getDescription();
  }

  public function getSettingString($name)
  {
    try
    {
      $setting = $this->settingManager->getSetting($name);
    }
    catch (SettingNotFoundException $e)
    {
      return '';
    }
    return $setting->getStringValue();
  }
}