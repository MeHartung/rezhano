<?php

namespace Accurateweb\SynchronizationBundle\Model\Datasource\Base;

abstract class BaseDataSource
{
  private $options = array();
  
  public function __construct($options = array())
  {
    $this->options = array_merge($this->options, $options);
  }
  
  abstract public function get($from, $to=null);
  
  protected function getOptions()
  {
    return $this->options;
  }
  
  protected function getSavedName()
  {
    return tempnam(sfConfig::get("sf_data_dir"), null);
  }
}