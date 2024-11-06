<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace Accurateweb\MediaBundle\Model\Image;


use Accurateweb\MediaBundle\Model\Media\ImageInterface;

interface ImageAwareInterface
{
  /**
   * @param $id
   * @return ImageInterface
   */
  public function getImage($id=null);

  /**
   * @param ImageInterface $image
   * @return mixed
   */
  public function setImage(ImageInterface $image);

  /**
   * @param $id
   * @return mixed
   */
  public function getImageOptions($id);

  public function setImageOptions($id);
}