<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 28.07.17
 * Time: 11:34
 */

namespace StoreBundle\Sluggable;

interface SluggableInterface {

  public function getSlugSource();

  public function getSlug();
  public function setSlug($slug);

}