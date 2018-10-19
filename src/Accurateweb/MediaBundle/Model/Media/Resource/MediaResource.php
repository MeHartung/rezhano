<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace Accurateweb\MediaBundle\Model\Media\Resource;

use Accurateweb\MediaBundle\Model\Media\MediaInterface;

class MediaResource implements MediaResourceInterface
{
  private $pathPrefix;

  private $urlPrefix;

  private $media;

  public function __construct(MediaInterface $media, $pathPrefix, $urlPrefix)
  {
    $this->media = $media;
    $this->pathPrefix = $pathPrefix;
    $this->urlPrefix = $urlPrefix;
  }

  public function getUrl()
  {
    return sprintf('%s/%s', $this->urlPrefix, $this->media->getResourceId());
  }

  public function getPath()
  {
    return sprintf('%s/%s', $this->pathPrefix, $this->fixResourceId($this->media->getResourceId()));
  }

  public function fileExists()
  {
    return is_file($this->getPath());
  }

  public function fixResourceId($resourceId)
  {
    return preg_replace('/^([\/\\\\]+)/', '', $resourceId);
  }
}