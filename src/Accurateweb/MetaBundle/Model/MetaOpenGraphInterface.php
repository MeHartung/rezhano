<?php

namespace Accurateweb\MetaBundle\Model;

use Accurateweb\MetaBundle\Model\OpenGraphType\OpenGraphType;

interface MetaOpenGraphInterface
{
  /**
   * @return string
   */
  public function getTitle();

  /**
   * @return OpenGraphType
   */
  public function getType();

  /**
   * @return string
   */
  public function getImage();

  /**
   * @return string
   */
  public function getUrl();

  /**
   * @return string|null
   */
  public function getAudio();

  /**
   * @return string|null
   */
  public function getDescription();

  /**
   * @return string|null
   */
  public function getDeterminer();

  /**
   * @return string|null
   */
  public function getLocale();

  /**
   * @return string|null
   */
  public function getLocaleAlternate();

  /**
   * @return string|null
   */
  public function getSiteName();

  /**
   * @return string|null
   */
  public function getVideo();
}