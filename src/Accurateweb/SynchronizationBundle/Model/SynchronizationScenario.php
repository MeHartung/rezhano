<?php

namespace Accurateweb\SynchronizationBundle\Model;

use Accurateweb\SynchronizationBundle\Model\Scenario\SynchronizationScenarioInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SynchronizationScenario implements SynchronizationScenarioInterface
{
  private $subjects = [],
          $iterator = null,
          $name;

  protected $dispatcher;
  
  /**
   * SynchronizationScenario constructor.
   *
   * @param EventDispatcherInterface|null $dispatcher
   * @param $name string
   */
  function __construct(EventDispatcherInterface $dispatcher = null, $name = '')
  {
    $this->setEventDispatcher($dispatcher);
    $this->name = $name;
  }

  public function getName()
  {
    return $this->name;
  }
  
  function addSubject($subject)
  {
    $this->subjects[] = $subject;
    $this->rewind();
  }

  function current()
  {
    return $this->subjects[$this->iterator];
  }

  function key()
  {
    return $this->iterator;
  }

  function next()
  {
    ++$this->iterator;
  }

  function rewind()
  {
    $this->iterator = 0;
  }

  function valid()
  {
    return isset($this->subjects[$this->iterator]);
  }

  function offsetExists($offset)
  {
    return isset($this->subjects[$offset]);
  }

  function offsetGet($offset)
  {
    return $this->offsetExists($offset) ? $this->subjects[$offset] : null;
  }

  function offsetSet($offset, $value)
  {
    if (is_null($offset))
    {
      $this->subjects[] = $value;
    }
    else
    {
      $this->subjects[$offset] = $value;
    }
  }

  function offsetUnset($offset)
  {
    unset($this->subjects[$offset]);
  }

  /**
   * Вызывается перед выполнением сценария
   */
  public function preExecute()
  {
  }

  /**
   * Вызывается после выполнения всего сценария.
   * 
   * Позволяет выполнить дополнительные задачи после выполнения сценария синхронизации, такие как очистка или запуск
   * внешних скриптов
   */
  public function postExecute()
  {    
  }

  public function setEventDispatcher(EventDispatcherInterface $dispatcher = null)
  {
    $this->dispatcher = $dispatcher;
  }
}
