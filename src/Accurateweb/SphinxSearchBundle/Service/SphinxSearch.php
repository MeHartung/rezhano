<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 17.06.2017
 * Time: 20:02
 */

namespace Accurateweb\SphinxSearchBundle\Service;

use Accurateweb\SphinxSearchBundle\Configuration\SearchdConfiguration;
use AccurateCommerce\Search\Sphinx\SphinxClient;

class SphinxSearch
{
  private $kernelRootDir,
          $options;

  public function __construct($kernelRootDir, $options)
  {
    $this->kernelRootDir = $kernelRootDir;
    $this->options = $options;
  }

  /**
   * @return SphinxClient
   */
  public function getSphinxClient()
  {
    return new SphinxClient($this->options);
  }

  /**
   * @return SearchdConfiguration
   */
  public function getSearchdOptions()
  {
    return new SearchdConfiguration($this->options);
  }

  public function getSphinxConfigFilePath()
  {
    $configurationDirectory = sprintf('%s%2$sconfig%2$ssphinx', $this->kernelRootDir, DIRECTORY_SEPARATOR);

    return sprintf( '%s%ssphinx.conf', $configurationDirectory, DIRECTORY_SEPARATOR );
  }
}