<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 15.09.2017
 * Time: 18:23
 */

namespace StoreBundle\DataAdapter;

use AccurateCommerce\DataAdapter\ClientApplicationModelAdapterInterface;

class RegionClientModelAdapter implements ClientApplicationModelAdapterInterface
{
  private $region;

  public function __construct($region)
  {
    $this->region = $region;
  }

  public function getClientModelName()
  {
    return 'Region';
  }

  public function getClientModelValues($context = null)
  {
    return array(
      'name' => $this->region
    );
  }

  public function getClientModelId()
  {

  }

}