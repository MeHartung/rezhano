<?php
/**
 * Copyright (c) 2017. Denis N. Ragozin <dragozin@accurateweb.ru>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

/*
 * Автор Денис Н. Рагозин <dragozin at accurateweb.ru>
 */

namespace AccurateCommerce\Build;

use AccurateCommerce\Exception\OperationNotSupportedException;

/**
 * Description of AssetMap
 *
 * @author Денис Н. Рагозин <dragozin at accurateweb.ru>
 */
class AssetMap implements \ArrayAccess
{
  private $map;
  
  public function __construct($rootDir)
  {
    $this->map = (include($rootDir.'/config/assets.config.php'));
  }

  /**
   * Whether a offset exists
   * @link http://php.net/manual/en/arrayaccess.offsetexists.php
   * @param mixed $offset <p>
   * An offset to check for.
   * </p>
   * @return boolean true on success or false on failure.
   * </p>
   * <p>
   * The return value will be casted to boolean if non-boolean was returned.
   * @since 5.0.0
   */
  public function offsetExists($offset)
  {
    return isset($this->map[$offset]);
  }

  /**
   * Offset to retrieve
   * @link http://php.net/manual/en/arrayaccess.offsetget.php
   * @param mixed $offset <p>
   * The offset to retrieve.
   * </p>
   * @return mixed Can return all value types.
   * @since 5.0.0
   */
  public function offsetGet($offset)
  {
    return isset($this->map[$offset]) ? $this->map[$offset] : null;
  }

  /**
   * Offset to set
   * @link http://php.net/manual/en/arrayaccess.offsetset.php
   * @param mixed $offset <p>
   * The offset to assign the value to.
   * </p>
   * @param mixed $value <p>
   * The value to set.
   * </p>
   * @return void
   * @since 5.0.0
   */
  public function offsetSet($offset, $value)
  {
    throw new OperationNotSupportedException();
  }

  /**
   * Offset to unset
   * @link http://php.net/manual/en/arrayaccess.offsetunset.php
   * @param mixed $offset <p>
   * The offset to unset.
   * </p>
   * @return void
   * @since 5.0.0
   */
  public function offsetUnset($offset)
  {
    throw new OperationNotSupportedException();
  }
}
