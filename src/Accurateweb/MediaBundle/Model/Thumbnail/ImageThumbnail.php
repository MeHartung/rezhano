<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace Accurateweb\MediaBundle\Model\Thumbnail;

use Accurateweb\MediaBundle\Exception\OperationNotSupportedException;
use Accurateweb\MediaBundle\Model\Media\ImageInterface;
use Accurateweb\MediaBundle\Model\Media\MediaInterface;

class ImageThumbnail implements MediaInterface
{
  private $id;

  private $resourceId;

  /**
   * @var ImageInterface
   */
  private $image;

  public function __construct($id, ImageInterface $image)
  {
    $this->id = $id;
    $this->image = $image;
  }

  public function getId()
  {
    return $this->id;
  }

  public function getResourceId()
  {
    if (null === $this->resourceId)
    {
      $imageResourceId = $this->image->getResourceId();

      $extension = pathinfo($imageResourceId, PATHINFO_EXTENSION);

      $this->resourceId = sprintf('%s/%s.%s',
        substr($imageResourceId, 0, -strlen($extension) - 1),
        $this->getId(),
        $extension
      );
    }

    return $this->resourceId;
  }

  public function setResourceId($id)
  {
    throw new OperationNotSupportedException();
  }

}