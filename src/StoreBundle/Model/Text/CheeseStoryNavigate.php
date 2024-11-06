<?php


namespace StoreBundle\Model\Text;

use StoreBundle\Entity\Text\CheeseStory;
use StoreBundle\Model\Navigate\SortableEntityNavigate;
use StoreBundle\Repository\Text\CheeseStoryRepository;

/**
 * Class CheeseStoryNavigate
 */
class CheeseStoryNavigate extends SortableEntityNavigate
{
  public function __construct(CheeseStoryRepository $cheeseStoryRepository, CheeseStory $cheeseStory)
  {
    parent::__construct($cheeseStoryRepository, $cheeseStory);
    
    $this->qb = $this->getQueryBuilder()->where('cs.text IS NOT NULL')->andWhere('cs.published = 1');
  }
}