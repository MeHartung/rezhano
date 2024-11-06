<?php

namespace StoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ContactPlace extends Constraint
{
  public $message = 'Вы уже выбрали опубликованный номер для указанной позиции';

  public function getTargets()
  {
    return self::CLASS_CONSTRAINT;
  }
}