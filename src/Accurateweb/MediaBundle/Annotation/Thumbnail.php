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
final class Thumbnail
{
  /**
   * @var string
   * @Required()
   */
  public $id;

  /**
   * @var array<\Accurateweb\MediaBundle\Annotation\Filter>
   */
  public $filters;
}