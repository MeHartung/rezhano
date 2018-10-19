<?php

namespace Accurateweb\SettingBundle\Model\Setting;


use Symfony\Component\Form\DataTransformerInterface;

interface SettingInterface
{
  /**
   * @return string
   */
  public function getName();

  /**
   * @return mixed
   */
  public function getValue();

  /**
   * @param $value
   * @return SettingInterface
   */
  public function setValue($value);

  /**
   * @return string
   */
  public function getFormType();

  /**
   * @return array
   */
  public function getFormOptions();

  /**
   * @return string
   */
  public function getStringValue ();

  /**
   * @return string
   */
  public function getDescription();

  /**
   * @return DataTransformerInterface|null
   */
  public function getModelTransformer();
}