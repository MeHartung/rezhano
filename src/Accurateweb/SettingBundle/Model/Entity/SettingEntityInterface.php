<?php

namespace Accurateweb\SettingBundle\Model\Entity;

interface SettingEntityInterface
{
  public function getName();

  public function getValue();

  public function setName($name);

  public function setValue($value);
}