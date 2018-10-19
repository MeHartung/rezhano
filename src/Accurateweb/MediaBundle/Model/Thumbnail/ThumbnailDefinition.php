<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace Accurateweb\MediaBundle\Model\Thumbnail;


use Accurateweb\ImagingBundle\Filter\FilterChain;

class ThumbnailDefinition
{
  private $id;

  private $filterChain;

  public function __construct($id, FilterChain $filterChain)
  {
    $this->id = $id;
    $this->filterChain = $filterChain;
  }

  /**
   * @return mixed
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @return FilterChain
   */
  public function getFilterChain()
  {
    return $this->filterChain;
  }
}