<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace Accurateweb\MediaBundle\Model\Gallery;

use Accurateweb\MediaBundle\Model\Media\MediaInterface;
//use Doctrine\Common\Persistence\ObjectManager;

interface MediaObjectManager //extends ObjectManager
{
  public function remove(MediaInterface $object);

  public function persist(MediaInterface $object);

  public function flush();

  /**
   * @return MediaRepositoryInterface
   */
  public function getRepository();
}