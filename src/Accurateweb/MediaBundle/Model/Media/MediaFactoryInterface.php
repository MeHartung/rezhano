<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace Accurateweb\MediaBundle\Model\Media;

interface MediaFactoryInterface
{
  /**
   * @return MediaInterface
   */
  public function create($id);
}