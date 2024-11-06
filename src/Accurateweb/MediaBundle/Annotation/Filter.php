<?php

namespace Accurateweb\MediaBundle\Annotation;

/**
 * Class Thumbnail
 *
 * @package Accurateweb\MediaBundle\Annotation
 *
 * @Annotation
 * @Target({"ALL"})
 */
final class Filter
{
  /**
   * @var string
   * @Required()
   */
  public $id;

  /**
   * @var array
   */
  public $options;
}