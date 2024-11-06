<?php

namespace StoreBundle\Event;

use StoreBundle\Entity\Text\Dialog\DialogMessage;
use Symfony\Component\EventDispatcher\Event;

class CustomerQuestionEvent extends Event
{
  private $customerQuestion;

  public function __construct (DialogMessage $customerQuestion)
  {
    $this->customerQuestion = $customerQuestion;
  }

  /**
   * @return DialogMessage
   */
  public function getCustomerQuestion ()
  {
    return $this->customerQuestion;
  }
}