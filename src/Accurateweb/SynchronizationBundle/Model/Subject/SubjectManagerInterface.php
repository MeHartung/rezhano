<?php

namespace Accurateweb\SynchronizationBundle\Model\Subject;


interface SubjectManagerInterface
{
  /**
   * @param $name string
   * @return SynchronizationSubjectInterface
   */
  public function get($name);
}