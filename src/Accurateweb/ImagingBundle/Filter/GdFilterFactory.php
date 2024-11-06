<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace Accurateweb\ImagingBundle\Filter;

use Accurateweb\ImagingBundle\Filter\GD\CropFilter;
use Accurateweb\ImagingBundle\Filter\GD\ResizeFilter;

class GdFilterFactory implements FilterFactoryInterface
{
  private $classMap;

  public function __construct()
  {
    $this->classMap = array(
      'resize' => ResizeFilter::class,
      'crop' => CropFilter::class
    );
  }

  /**
   * @param $id
   * @param $options
   * @return ImageFilterInterface
   * @throws \Exception
   */
  public function create($id, array $options = array())
  {
    if (!isset($this->classMap[$id]))
    {
      throw new \Exception();
    }

    $filterClassName = $this->classMap[$id];

    return new $filterClassName($options);
  }
}