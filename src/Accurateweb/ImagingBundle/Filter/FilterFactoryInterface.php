<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace Accurateweb\ImagingBundle\Filter;


interface FilterFactoryInterface
{
  /**
   * @param $id
   * @param array $options
   * @return ImageFilterInterface
   */
  public function create($id, array $options = array());
}