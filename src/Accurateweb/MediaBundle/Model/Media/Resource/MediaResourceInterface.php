<?php

namespace Accurateweb\MediaBundle\Model\Media\Resource;

/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */
interface MediaResourceInterface
{
  /**
   * Returns an resource URL
   *
   * @return string
   */
  public function getUrl();

  /**
   * Returns an resource path
   *
   * @return mixed
   */
  public function getPath();
}