<?php

namespace StoreBundle\DataAdapter\Text;

use Accurateweb\ClientApplicationBundle\DataAdapter\ClientApplicationModelAdapterInterface;
use StoreBundle\Entity\Text\Article;

class ArticleAdapter implements ClientApplicationModelAdapterInterface
{
  /**
   * @param Article $subject
   * @param array $options
   * @return array
   */
  public function transform ($subject, $options = array())
  {
    return [
      'id' => $subject->getId(),
      'slug' => $subject->getSlug(),
      'title' => $subject->getTitle(),
      'text' => $subject->getText(),
    ];
  }

  public function getModelName ()
  {
    return 'Article';
  }

  public function supports ($subject)
  {
    return $subject instanceof Article;
  }

}