<?php

namespace Accurateweb\MetaBundle\Twig;


use Accurateweb\MetaBundle\Manager\MetaOpenGraphManagerInterface;

class MetaOpenGraphExtension extends \Twig_Extension
{
  private $manager;

  public  function __construct(MetaOpenGraphManagerInterface $manager)
  {
    $this->manager = $manager;
  }

  public function getFunctions()
  {
    return array(
      new \Twig_SimpleFunction('ogMeta', array($this, 'getMetaOpenGraph')),
      new \Twig_SimpleFunction('ogSiteName', array($this, 'getSiteName'), array('is_safe' => array('html'))),
      new \Twig_SimpleFunction('ogTitle', array($this, 'getTitle'), array('is_safe' => array('html'))),
      new \Twig_SimpleFunction('ogType', array($this, 'getType'), array('is_safe' => array('html'))),
      new \Twig_SimpleFunction('ogDescription', array($this, 'getDescription'), array('is_safe' => array('html'))),
      new \Twig_SimpleFunction('ogImage', array($this, 'getImage'), array('is_safe' => array('html'))),
    );
  }

  public function getMetaOpenGraph()
  {
    return $this->manager->getMetaOpenGraph();
  }

  public function getSiteName()
  {
    if ($this->getMetaOpenGraph())
    {
      return $this->getMetaOpenGraph()->getSiteName();
    }

    return null;
  }

  public function getTitle()
  {
    if ($this->getMetaOpenGraph())
    {
      return $this->getMetaOpenGraph()->getTitle();
    }

    return null;
  }

  public function getType()
  {
    if ($this->getMetaOpenGraph())
    {
      $type = $this->getMetaOpenGraph()->getType();
      return $type?$type->getName():'website';
    }

    return null;
  }

  public function getDescription()
  {
    if ($this->getMetaOpenGraph())
    {
      return $this->getMetaOpenGraph()->getDescription();
    }

    return null;
  }

  public function getImage()
  {
    if ($this->getMetaOpenGraph())
    {
      return $this->getMetaOpenGraph()->getImage();
    }

    return null;
  }
}