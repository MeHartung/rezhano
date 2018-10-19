<?php

namespace StoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class Ogrn extends Constraint
{
  public $message = 'Не соответствует формату ОГРН / ОГРНиП';
}