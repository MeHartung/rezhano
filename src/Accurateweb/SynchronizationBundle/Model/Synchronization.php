<?php

namespace Accurateweb\SynchronizationBundle\Model;

/**
 * Предоставляет функции управления параметрами конкретной синхронизации
 */
class Synchronization
{
  private $subject;
  private $service;
  private $mode;
  private $direction;

  public function __construct($service, $subject, $mode=SynchronizationMode::INCREMENTAL, $direction=SynchronizationDirection::INCOMING)
  {
    $this->service = $service;
    $this->mode = $mode;
    $this->direction = $direction;
    $this->subject = $subject;
  }

  public function execute($parameters=array())
  {
    switch ($this->direction)
    {
      case SynchronizationDirection::INCOMING:
        {
          $options = array_merge($parameters, array(
            'mode' => $this->mode
          ));

          return $this->service->pull($this->subject, $options);
          break;
        }
      case SynchronizationDirection::OUTGOING:
        {
          $options = $parameters;
          return $this->service->push($this->subject, $options);
          break;
        }
      default: throw new asSynchronizationException(sprintf('Неверное направление синхронизации "%s"', $this->direction));
    }
  }

  public function getMode()
  {
    return $this->mode;
  }

  public function getDirection()
  {
    return $this->direction;
  }

  public function getSubjectName()
  {
    return $this->subject;
  }
}
