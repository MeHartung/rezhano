<?php

namespace Accurateweb\MoyskladIntegrationBundle\Model;

use Accurateweb\MoyskladIntegrationBundle\Repository\MoyskladRepository;
use Accurateweb\SettingBundle\Model\Setting\SettingInterface;
use MoySklad\MoySklad;

class MoyskladManager
{
  private $sklad;
  private $repositories;

  public function __construct (SettingInterface $username, SettingInterface$password)
  {
    $this->sklad = MoySklad::getInstance($username->getValue(), $password->getValue());
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