<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace Accurateweb\MediaBundle\Model\Gallery;


interface MediaGalleryProviderInterface
{
  /**
   * @param $id
   * @return MediaGalleryInterface
   */
  public function provide($id);
}