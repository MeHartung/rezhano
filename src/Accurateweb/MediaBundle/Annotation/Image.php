<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace Accurateweb\MediaBundle\Annotation;
use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class Image
 *
 * @package Accurateweb\MediaBundle\Annotation
 *
 * @Annotation
 * @Target({"PROPERTY"})
 */
final class Image
{
  /**
   * @var string
   * @Required()
   */
  public $id;

  /**
   * @var array<\Accurateweb\MediaBundle\Annotation\Thumbnail>
   */
  public $thumbnails;
}