<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace Accurateweb\MediaBundle\Model\Media;

interface MediaInterface
{
  public function getId();

  public function getResourceId();

  public function setResourceId($id);
}