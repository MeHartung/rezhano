<?php

namespace Accurateweb\MetaBundle\Model;

use Accurateweb\MetaBundle\Model\OpenGraphType\OpenGraphType;

class MetaOpenGraph implements MetaOpenGraphInterface
{
  /**
   * @var string
   */
  protected $title;

  /**
   * @var OpenGraphType
   */
  protected $type;
  /**
   * @var string
   */
  protected $image;

  /**
   * @var string
   */
  protected $url;

  /**
   * @var string
   */
  protected $audio;

  /**
   * @var string
   */
  protected $description;

  /**
   * @var string
   */
  protected $determiner;

  /**
   * @var string
   */
  protected $locale;

  /**
   * @var string
   */
  protected $localeAlternate;

  /**
   * @var string
   */
  protected $siteName;

  /**
   * @var string
   */
  protected $video;

  /**
   * @return string
   */
  public function getTitle ()
  {
    return $this->title;
  }

  /**
   * @param string $title
   * @return $this
   */
  public function setTitle ($title)
  {
    $this->title = $title;
    return $this;
  }

  /**
   * @return OpenGraphType
   */
  public function getType ()
  {
    return $this->type;
  }

  /**
   * @param OpenGraphType $type
   * @return $this
   */
  public function setType ($type)
  {
    $this->type = $type;
    return $this;
  }

  /**
   * @return string
   */
  public function getImage ()
  {
    return $this->image;
  }

  /**
   * @param string $image
   * @return $this
   */
  public function setImage ($image)
  {
    $this->image = $image;
    return $this;
  }

  /**
   * @return string
   */
  public function getUrl ()
  {
    return $this->url;
  }

  /**
   * @param string $url
   * @return $this
   */
  public function setUrl ($url)
  {
    $this->url = $url;
    return $this;
  }

  /**
   * @return string
   */
  public function getAudio ()
  {
    return $this->audio;
  }

  /**
   * @param string $audio
   * @return $this
   */
  public function setAudio ($audio)
  {
    $this->audio = $audio;
    return $this;
  }

  /**
   * @return string
   */
  public function getDescription ()
  {
    return $this->description;
  }

  /**
   * @param string $description
   * @return $this
   */
  public function setDescription ($description)
  {
    $this->description = $description;
    return $this;
  }

  /**
   * @return string
   */
  public function getDeterminer ()
  {
    return $this->determiner;
  }

  /**
   * @param string $determiner
   * @return $this
   */
  public function setDeterminer ($determiner)
  {
    $this->determiner = $determiner;
    return $this;
  }

  /**
   * @return string
   */
  public function getLocale ()
  {
    return $this->locale;
  }

  /**
   * @param string $locale
   * @return $this
   */
  public function setLocale ($locale)
  {
    $this->locale = $locale;
    return $this;
  }

  /**
   * @return string
   */
  public function getLocaleAlternate ()
  {
    return $this->localeAlternate;
  }

  /**
   * @param string $localeAlternate
   * @return $this
   */
  public function setLocaleAlternate ($localeAlternate)
  {
    $this->localeAlternate = $localeAlternate;
    return $this;
  }

  /**
   * @return string
   */
  public function getSiteName ()
  {
    return $this->siteName;
  }

  /**
   * @param string $siteName
   * @return $this
   */
  public function setSiteName ($siteName)
  {
    $this->siteName = $siteName;
    return $this;
  }

  /**
   * @return string
   */
  public function getVideo ()
  {
    return $this->video;
  }

  /**
   * @param string $video
   * @return $this
   */
  public function setVideo ($video)
  {
    $this->video = $video;
    return $this;
  }
}