<?php


namespace AppBundle\DataAdapter\Text;

use Accurateweb\ClientApplicationBundle\DataAdapter\ClientApplicationModelAdapterInterface;
use AppBundle\Entity\Text\CheeseStory;

class CheeseStoryAdapter implements ClientApplicationModelAdapterInterface
{
  private $story;
  
  public function __construct(CheeseStory $cheeseStory)
  {
    $this->story = $cheeseStory;
  }
  
  /**
   * @param $subject CheeseStory
   * @param array $options
   * @return array
   */
  public function transform($subject, $options = array())
  {
    return [
      'id' => $subject->getId(),
      'title' => $subject->getTitle(),
      'text' => $subject->getText(),
      'position' => $subject->getPosition(),
      'photo' => $subject->getTeaser()
    ];
  }
  
  public function getModelName()
  {
    return 'CheeseStory';
  }
  
  public function supports($subject)
  {
    return $subject instanceof CheeseStory;
  }
}