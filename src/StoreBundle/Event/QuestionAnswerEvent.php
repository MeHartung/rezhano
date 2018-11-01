<?php

namespace StoreBundle\Event;

use StoreBundle\Entity\Text\Question;
use Symfony\Component\EventDispatcher\Event;

class QuestionAnswerEvent extends Event
{
  private $question;
  
  public function __construct (Question $customerQuestion)
  {
    $this->question = $customerQuestion;
  }
  
  /**
   * @return Question
   */
  public function getQuestion ()
  {
    return $this->question;
  }
}