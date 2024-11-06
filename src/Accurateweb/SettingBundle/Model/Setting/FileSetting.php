<?php

namespace Accurateweb\SettingBundle\Model\Setting;

use Accurateweb\SettingBundle\Model\Storage\SettingStorageInterface;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileSetting implements SettingInterface
{
  private $name;
  private $description;
  private $default;
  private $settingStorage;
  private $file_dir;

  public function __construct (SettingStorageInterface $settingStorage, $name, $description, $default, $file_dir)
  {
    $this->settingStorage = $settingStorage;
    $this->name = $name;
    $this->description = $description;
    $this->default = $default;
    $this->file_dir = $file_dir;
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
    return 'file';
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
    return new CallbackTransformer(array($this, 'transformValue'), array($this, 'reverseTransform'));
  }

  public function transformValue ($value)
  {
    if ($value instanceof UploadedFile)
    {
      return $value;
    }

    $saved_file = sprintf("%s/%s", $this->getFileDir(), $value);

    if (!\file_exists($saved_file) || !is_file($saved_file))
    {
      return null;
    }

    $transformed = new UploadedFile($saved_file, $value);

    return $transformed;
  }

  public function reverseTransform ($value)
  {
    if ($value == null && $this->getValue())
    {
      return $this->getValue();
    }

    if (!$value instanceof UploadedFile)
    {
      throw new \Exception('Ожидается файл');
    }

    $name = sprintf("%s.%s", uniqid($this->getName()), $value->getClientOriginalExtension());

    if ($value->move($this->getFileDir(), $name))
    {
      $original_file = sprintf("%s/%s", $this->getFileDir(), $this->getValue());

      if (\file_exists($original_file))
      {
        @unlink($original_file);
      }
    }

    $transformed = $name;

    return $transformed;
  }

  protected function getFileDir()
  {
    return $this->file_dir;
  }
}