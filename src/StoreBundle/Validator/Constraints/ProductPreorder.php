<?php

namespace StoreBundle\Validator\Constraints;


use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ProductPreorder extends Constraint
{

  public $message = 'Для предзакза необходимо указать ожидаемую дату поставки товара';

  public function getTargets()
  {
    return self::CLASS_CONSTRAINT;
  }

}