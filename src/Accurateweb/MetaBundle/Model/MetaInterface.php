<?php

namespace Accurateweb\MetaBundle\Model;

interface MetaInterface
{
  /**
   * @return string
   */
  public function getMetaTitle();

  /**
   * @return string|null
   */
  public function getMetaDescription();

  /**
   * @return string|null
   */
  public function getMetaKeywords();
}