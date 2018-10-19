<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 17.06.2017
 * Time: 20:46
 */

namespace Accurateweb\SphinxSearchBundle\Configuration;

use Sonata\BlockBundle\Util\OptionsResolver;

class SearchdConfiguration
{
  private $binaryPath;

  private $host;

  private $port;

  private $socket;

  public function __construct($options)
  {
    $resolver = new OptionsResolver();
    $resolver->setDefaults(array(
      'binary_path' => null,
      'host' => 'localhost',
      'port' => 9312,
      'limit' => 20
    ));

    $options = $resolver->resolve($options);

    $this->binaryPath = $options['binary_path'];
    $this->host = $options['host'];
    $this->port = $options['port'];
  }

  /**
   * @return mixed
   */
  public function getBinaryPath()
  {
    return $this->binaryPath;
  }

  /**
   * @param mixed $binaryPath
   * @return SearchdConfiguration
   */
  public function setBinaryPath($binaryPath)
  {
    $this->binaryPath = $binaryPath;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getHost()
  {
    return $this->host;
  }

  /**
   * @param mixed $host
   * @return SearchdConfiguration
   */
  public function setHost($host)
  {
    $this->host = $host;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getPort()
  {
    return $this->port;
  }

  /**
   * @param mixed $port
   * @return SearchdConfiguration
   */
  public function setPort($port)
  {
    $this->port = $port;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getSocket()
  {
    return $this->socket;
  }

  /**
   * @param mixed $socket
   * @return SearchdConfiguration
   */
  public function setSocket($socket)
  {
    $this->socket = $socket;

    return $this;
  }


}