<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace Accurateweb\MediaBundle\Model\Image;


use Accurateweb\MediaBundle\Model\Media\ImageInterface;
use Accurateweb\MediaBundle\Model\Thumbnail\ImageThumbnail;

abstract class Image implements ImageInterface
{
  private $resourceId;

  private $id;

  function __construct($id, $resourceId)
  {
    $this->id = $id;
    $this->resourceId = $resourceId;
  }

  public function getId()
  {
    return $this->id;
  }

  public function getResourceId()
  {
    return $this->resourceId;
  }

  public function setResourceId($id)
  {
    $this->resourceId = $id;
  }

  /**
   * Returns thumbnail for an image
   *
   * @param $id
   * @return ImageThumbnail
   * @throws \Exception
   */
  public function getThumbnail($id)
  {
    $definitions = $this->getThumbnailDefinitions();

    if (!isset($definitions[$id]))
    {
      throw new \Exception(sprintf('Thumbnail "%s" is not defined', $id));
    }

    return new ImageThumbnail($id, $this);
  }

}