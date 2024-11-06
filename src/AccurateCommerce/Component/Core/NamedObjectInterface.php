<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 25.06.2017
 * Time: 14:02
 */

namespace AccurateCommerce\Component\Core;


interface NamedObjectInterface
{
  public function getName();

  public function setName($name);
}