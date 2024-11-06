<?php

namespace Accurateweb\MediaBundle\Model\Media;


interface MediaCroppableInterface
{
  public function getCrop();

  public function setCrop($crop);
}