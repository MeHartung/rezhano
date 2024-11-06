<?php

namespace StoreBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\File;

class Base64Transformer implements DataTransformerInterface
{
  public function transform ($value)
  {
    if ($value instanceof File && $value->isReadable())
    {
      $content = file_get_contents($value->getPath());
      return base64_encode($content);
    }

    return $value;
  }

  public function reverseTransform ($value)
  {
    if (!is_string($value))
    {
      return $value;
    }

    $tmp = tempnam(sys_get_temp_dir(), 'b');
//    $mime = null;
//    $ext = null;
//
//    if (preg_match('/data\:\w+\/(\w+)\;base64,/', $value, $m))
//    {
//      $ext = $m[1];
//    }
//
//    if (preg_match('/data\:(\w+\/\w+)\;base64,/', $value, $m))
//    {
//      $mime = $m[1];
//    }

    $content = preg_replace('/data\:\w+\/\w+\;base64,/', '', $value);
    file_put_contents($tmp, base64_decode($content));
    $file = new File($tmp, true);
    return $file;
  }
}