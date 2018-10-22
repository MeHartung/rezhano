<?php

namespace Accurateweb\MoyskladIntegrationBundle\Model;

use Accurateweb\MoyskladIntegrationBundle\Repository\MoyskladRepository;
use MoySklad\MoySklad;

class MoyskladManager
{
  private $sklad;
  private $repositories;

  public function __construct ($username, $password)
  {
    $this->sklad = MoySklad::getInstance($username, $password);
    $this->repositories = array();
  }

  /**
   * @return \Moysklad\MoySklad
   */
  public function getSklad()
  {
    return $this->sklad;
  }

  /**
   * @param $class
   * @return MoyskladRepository
   */
  public function getRepository($class)
  {
    if (!isset($this->repositories[$class]))
    {
      $this->repositories[$class] = new MoyskladRepository($this->sklad, $class);
    }

    return $this->repositories[$class];
  }
}