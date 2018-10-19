<?php

namespace Accurateweb\MetaBundle\Twig;

use Accurateweb\MetaBundle\Manager\MetaManagerInterface;
use Accurateweb\MetaBundle\Model\Meta;
use Accurateweb\MetaBundle\Model\MetaInterface;

class MetaExtension extends \Twig_Extension
{
  private $manager;

  public  function __construct(MetaManagerInterface $manager)
  {
    $this->manager = $manager;
  }

  public function getFunctions()
  {
    return array(
      new \Twig_SimpleFunction('meta', array($this, 'getMeta')),
      new \Twig_SimpleFunction('metaTitle', array($this, 'getMetaTitle'), array('is_safe' => array('html'))),
      new \Twig_SimpleFunction('metaDescription', array($this, 'getMetaDescription'), array('is_safe' => array('html'))),
      new \Twig_SimpleFunction('metaKeywords', array($this, 'getMetaKeywords'), array('is_safe' => array('html'))),
    );
  }

  /**
   * @return MetaInterface|null
   */
  public function getMeta()
  {
    return $this->manager->getMeta();
  }

  /**
   * @return string
   */
  public function getMetaTitle()
  {
    return $this->getMeta()->getMetaTitle()?$this->getMeta()->getMetaTitle():'';
  }

  /**
   * @return string
   */
  public function getMetaDescription()
  {
    return $this->getMeta()->getMetaDescription()?$this->getMeta()->getMetaDescription():'';
  }

  /**
   * @return string
   */
  public function getMetaKeywords()
  {
    return $this->getMeta()->getMetaKeywords()?$this->getMeta()->getMetaKeywords():'';
  }
}