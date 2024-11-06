<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace Accurateweb\MediaBundle\Model\Image;


use Accurateweb\MediaBundle\Model\Media\ImageInterface;
use Accurateweb\MediaBundle\Model\Thumbnail\ImageThumbnail;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class Image implements ImageInterface
{
  private $resourceId;

  private $id;

  private $options;

  function __construct($id, $resourceId, $options=array())
  {
    $this->id = $id;
    $this->resourceId = $resourceId;

    $resolver = new OptionsResolver();

    $this->configureOptions($resolver);

    $this->options = $resolver->resolve($options);
  }

  protected function configureOptions(OptionsResolver $resolver)
  {

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
   * @param string $id
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

  protected function getOptions()
  {
    return $this->options;
  }

  protected function getOption($name)
  {
    return isset($this->options[$name]) ? $this->options[$name] : null;
  }
}