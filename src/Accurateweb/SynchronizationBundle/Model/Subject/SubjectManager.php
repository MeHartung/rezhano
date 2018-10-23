<?php

namespace Accurateweb\SynchronizationBundle\Model\Subject;


class SubjectManager implements SubjectManagerInterface
{
  /** @var SynchronizationSubjectInterface[] */
  private $synchronizationSubjectPool;
  
  public function __construct ()
  {
    $this->synchronizationSubjectPool = array();
  }
  
  /**
   * @param $alias
   * @return SynchronizationSubjectInterface
   */
  public function get($alias)
  {
    return $this->synchronizationSubjectPool[$alias];
  }
  
  public function getAll()
  {
    return $this->synchronizationSubjectPool;
  }
  
  public function addScenario (SynchronizationSubjectInterface $subject)
  {
    $alias = $subject->getName();
    $this->synchronizationSubjectPool[$alias] = $subject;
  }
}