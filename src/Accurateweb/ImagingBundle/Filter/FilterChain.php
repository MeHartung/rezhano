<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace Accurateweb\ImagingBundle\Filter;

class FilterChain implements \Iterator
{
  private $filters;

  public function __construct($filters)
  {
    $this->filters = $filters;
  }

  public function add($id, array $options = array())
  {
    $this->filters[] = array(
      'id' => $id,
      'options' => $options
    );
  }

  /**
   * Return the current element
   * @link http://php.net/manual/en/iterator.current.php
   * @return mixed Can return any type.
   * @since 5.0.0
   */
  public function current()
  {
    return current($this->filters);
  }

  /**
   * Move forward to next element
   * @link http://php.net/manual/en/iterator.next.php
   * @return void Any returned value is ignored.
   * @since 5.0.0
   */
  public function next()
  {
    return next($this->filters);
  }

  /**
   * Return the key of the current element
   * @link http://php.net/manual/en/iterator.key.php
   * @return mixed scalar on success, or null on failure.
   * @since 5.0.0
   */
  public function key()
  {
    return key($this->filters);
  }

  /**
   * Checks if current position is valid
   * @link http://php.net/manual/en/iterator.valid.php
   * @return boolean The return value will be casted to boolean and then evaluated.
   * Returns true on success or false on failure.
   * @since 5.0.0
   */
  public function valid()
  {
    return isset($this->filters[$this->key()]);
  }

  /**
   * Rewind the Iterator to the first element
   * @link http://php.net/manual/en/iterator.rewind.php
   * @return void Any returned value is ignored.
   * @since 5.0.0
   */
  public function rewind()
  {
    reset($this->filters);
  }


}