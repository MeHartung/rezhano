<?php

namespace Accurateweb\SettingBundle\Model\Setting;

use Accurateweb\SettingBundle\Model\Storage\SettingStorageInterface;
use BaseObject;
use Symfony\Component\Form\CallbackTransformer;

class PropelEntitySetting implements SettingInterface
{
  private $name;
  private $description;
  private $settingStorage;
  private $class;
  private $options;

  public function __construct (SettingStorageInterface $settingStorage, $class, $name, $description, $options=null)
  {
    $this->settingStorage = $settingStorage;
    $this->name = $name;
    $this->description = $description;
    $this->class = $class;

    if (!is_array($options))
    {
      $options = array();
    }

    $this->options = $options;
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
      return null;
    }

    $query_class = $this->class.'Query';

    if (empty($this->options['multiple']))
    {
      return $query_class::create()->findPk($value);
    }

    return $query_class::create()->filterByPrimaryKeys(explode(',', $value))->find();
  }

  /**
   * @param $values BaseObject[]
   * @return SettingInterface|void
   * @throws \Exception
   */
  public function setValue ($values)
  {
    if (empty($this->options['multiple']))
    {
      if (!$values instanceof $this->class)
      {
        throw new \Exception(sprintf('Value should be instance of %s', $this->class));
      }

      $id = $values->getId(); //бида
      $this->settingStorage->set($this->name, $id);
      return;
    }

    $vals = implode(',', $values);
    $this->settingStorage->set($this->name, $vals);
  }

  public function getFormType ()
  {
//    return 'entity';
    return 'choice';
  }

  public function getFormOptions ()
  {
    $formoptions = array(
      'required' => false,
      'choices' => $this->getChoices()
    );

    if (!empty($this->options['multiple']))
    {
      $formoptions['multiple'] = true;
    }

    return $formoptions;
  }

  public function getStringValue ()
  {
    if (is_null($this->getValue()))
    {
      return '';
    }

    if (!$this->options['multiple'])
    {
      $values = array($this->getValue());
    }
    else
    {
      $values = $this->getValue();
    }

    $ret = [];

    foreach ($values as $value)
    {
      if (method_exists($value, '__toString'))
      {
        $ret[] = $value->__toString();
      }
      else
      {
        $ret[] = sprintf('%s[%s]', $this->class, $value->getId());
      }
    }

    return implode(',', $ret);
  }

  public function getDescription ()
  {
    return $this->description;
  }

  private function getChoices()
  {
    $choices = array();
    $query_class = $this->class.'Query';


    foreach ($query_class::create()->find() as $item)
    {
      $choices[$item->getId()] = (string)$item;
//      $choices[(string)$item] = $item->getId();
    }

    return $choices;
  }

  public function getModelTransformer()
  {
    if (!empty($this->options['multiple']))
    {
      return new CallbackTransformer([$this, 'transform'], [$this, 'reverseTransform']);
    }

    return null;
  }

  public function transform($subject)
  {
    if (!is_array($subject))
    {
      return explode(',', $subject);
    }

    return $subject;
  }

  public function reverseTransform($subject)
  {
    return $subject;
  }
}