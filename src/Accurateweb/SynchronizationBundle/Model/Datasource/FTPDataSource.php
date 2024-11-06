<?php

namespace Accurateweb\SynchronizationBundle\Model\Datasource;

use Accurateweb\SynchronizationBundle\Model\Connection\FTPConnection;
use Accurateweb\SynchronizationBundle\Model\Datasource\Base\BaseDataSource;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class FTPDataSource extends BaseDataSource
{
  private $connection;

  public function __construct($options = array())
  {
    parent::__construct($options);
    $this->connection = new FTPConnection($options);
  }

  public function get($from, $to = null)
  {
    if ($to === null)
    {
      $to = $this->getSavedName();
    }
    $ftp = $this->connection->connect();

    if (!$ftp)
    {
      throw new BadCredentialsException('Can not connect to FTP server');
    }

    if (!$this->connection->get($from, $to))
    {
      throw new BadCredentialsException('File download error');
    }

    $this->connection->disconnect();
    return $to;
  }

  public function put($from, $to)
  {
    $ftp = $this->connection->connect();

    if (!$ftp)
    {
      throw new BadCredentialsException('Can not connect to FTP server');
    }

    if (!$this->connection->put($from, $to))
    {
      throw new BadCredentialsException('File download error');
    }

    $this->connection->disconnect();
  }

  public function getConnection()
  {
    return $this->connection;
  }

}
