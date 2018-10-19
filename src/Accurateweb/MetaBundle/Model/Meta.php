<?php

namespace Accurateweb\MetaBundle\Model;

class Meta implements MetaInterface
{
  /**
   * @var string
   */
  private $metaTitle;

  /**
   * @var string
   */
  private $metaDescription;

  /**
   * @var string
   */
  private $metaKeywords;

  public function __construct ($metaTitle='', $metaDescription='', $metaKeywords='')
  {
    $this->metaTitle = $metaTitle;
    $this->metaDescription = $metaDescription;
    $this->metaKeywords = $metaKeywords;
  }

  /**
   * @return string
   */
  public function getMetaTitle ()
  {
    return $this->metaTitle;
  }

  /**
   * @param string $metaTitle
   * @return $this
   */
  public function setMetaTitle ($metaTitle)
  {
    $this->metaTitle = $metaTitle;
    return $this;
  }

  /**
   * @return string
   */
  public function getMetaDescription ()
  {
    return $this->metaDescription;
  }

  /**
   * @param string $metaDescription
   * @return $this
   */
  public function setMetaDescription ($metaDescription)
  {
    $this->metaDescription = $metaDescription;
    return $this;
  }

  /**
   * @return string
   */
  public function getMetaKeywords ()
  {
    return $this->metaKeywords;
  }

  /**
   * @param string $metaKeywords
   * @return $this
   */
  public function setMetaKeywords ($metaKeywords)
  {
    $this->metaKeywords = $metaKeywords;
    return $this;
  }
}