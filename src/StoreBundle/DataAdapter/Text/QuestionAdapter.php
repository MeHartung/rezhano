<?php

namespace StoreBundle\DataAdapter\Text;

use Accurateweb\ClientApplicationBundle\DataAdapter\ClientApplicationModelAdapterInterface;
use StoreBundle\Entity\Text\Question;

class QuestionAdapter implements ClientApplicationModelAdapterInterface
{
  /**
   * @param Question $subject
   * @param array $options
   * @return array
   */
  public function transform ($subject, $options = array())
  {
    return [
      'id' => $subject->getId(),
      'text' => $subject->getText(),
      'email' => $subject->getEmail(),
      'phone' => $subject->getPhone(),
      'fio' => $subject->getFio(),
    ];
  }

  public function getModelName ()
  {
    return 'Question';
  }

  public function supports ($subject)
  {
    return $subject instanceof Question;
  }

}