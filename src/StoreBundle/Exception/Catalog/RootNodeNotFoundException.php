<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 11.09.2018
 * Time: 16:15
 */

namespace StoreBundle\Exception\Catalog;


use Throwable;

class RootNodeNotFoundException extends \Exception
{
  public function __construct()
  {
    parent::__construct('Не найден корневой раздел каталога.');
  }
}