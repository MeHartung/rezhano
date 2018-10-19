<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 29.12.17
 * Time: 20:25
 */

namespace StoreBundle\Twig;


use Twig\TwigFilter;

class PhoneExtension extends \Twig_Extension
{

  public function getFilters()
  {
    return array(
      new TwigFilter('calltouchPhone', array($this, 'calltouchPhone'), array('needs_environment' => true)),
    );
  }

  public function calltouchPhone(\Twig_Environment $environment, $content, $type)
  {
    if($type == 'msk')
    {
      $phone = '<span class="call_phone_1">' .$content. '</span>';
    }

    if($type == 'ekb')
    {
      $phone = '<span class="call_phone_2">' .$content. '</span>';
    }

    if ($type == 'text')
    {
      $phone = '<span class="call_phone_text">' .$content. '</span>';
    }

    return $phone;
  }

}