<?php

namespace Accurateweb\TaxonomyBundle\Model\TaxonPresentation;


trait ParameterContainerTrait
{
  private $parameters;

  public function getParameters ()
  {
    return $this->parameters;
  }

  public function setParameters ($parameters)
  {
    $this->parameters = $parameters;
    return $this;
  }

  public function addParameter ($param, $value)
  {
    $this->parameters[$param] = $value;
    return $this;
  }

  public function hasParameter ($param, $value)
  {
    return isset($this->parameters[$param]) || array_key_exists($param, $this->parameters);
  }

  public function getParameter ($param)
  {
    return $this->parameters[$param];
  }
}