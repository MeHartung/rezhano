<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 04.10.2017
 * Time: 17:31
 */

namespace Accurateweb\SlugifierBundle\Model;


interface SlugifierInterface
{
  /**
   * Slugifies text
   *
   * @param string $text Text to slugify, i.e. Text article title
   * @param string $separator A char (or string) used as a separator for words. This must be a non-space character.
   * @return string Slugified text, i.e. text-article-title
   */
  public function slugify($text, $separator='-');
}