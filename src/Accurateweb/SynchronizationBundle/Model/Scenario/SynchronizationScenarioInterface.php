<?php

namespace Accurateweb\SynchronizationBundle\Model\Scenario;


use Symfony\Component\EventDispatcher\EventDispatcherInterface;

interface SynchronizationScenarioInterface extends \Iterator, \ArrayAccess
{
  /**
   * @return string
   */
  public function getName();
  /**
   * @param $subject
   * @return mixed
   */
  function addSubject($subject);
  
  /**
   * Вызывается перед выполнением сценария
   * @return void
   */
  public function preExecute();
  
  /**
   * Вызывается после выполнения всего сценария.
   *
   * Позволяет выполнить дополнительные задачи после выполнения сценария синхронизации, такие как очистка или запуск
   * внешних скриптов
   * @param array $subjects
   * @return void
   */
  public function postExecute($subjects);
  
  /**
   * @param EventDispatcherInterface|null $dispatcher
   * @return void
   */
  public function setEventDispatcher(EventDispatcherInterface $dispatcher = null);
}