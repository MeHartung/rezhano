<?php

namespace Accurateweb\SettingBundle\Model\Setting;


use Accurateweb\SettingBundle\Model\Storage\SettingStorageInterface;
use Doctrine\Common\Collections\ArrayCollection;

class JsonArraySetting implements SettingInterface
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
    $this->default = new ArrayCollection();
  }
  
  public function getName ()
  {
    return $this->name;
  }
  
  public function getValue ()
  {
    $ar = new ArrayCollection();
    foreach ( json_decode($this->default, true) as $item)
    {
      $ar->add($item);
    }
    
    return $ar;
/*    $value = $this->settingStorage->get($this->name);
   # var_dump($value);die;
    if (is_null($value))
    {
      #return json_decode($this->default, true);
      return json_decode($this->default, true);
    }
    
    return json_decode($value, true);*/
  }
  
  public function setValue ($value)
  {
    $value = json_encode($value);
    $this->settingStorage->set($this->name, $value);
  }
  
  public function getFormType ()
  {
    return 'Symfony\Component\Form\Extension\Core\Type\CollectionType';
  }
  
  public function getFormOptions ()
  {
    return array();
  }
  
  public function getStringValue ()
  {
    $ar = new ArrayCollection();
    foreach ( json_decode($this->default, true) as $item)
    {
      $ar->add($item);
    }
    
    return $ar;
    #return (string)json_encode($this->getValue());
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