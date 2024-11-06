<?php

namespace Accurateweb\SettingBundle\Model\Manager;

use Accurateweb\SettingBundle\Model\ProxyQuery;
use Sonata\DoctrineORMAdminBundle\Model\ModelManager as BaseModelManager;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ModelManager extends BaseModelManager
{
  protected $settingManager;

  function __construct(RegistryInterface $registry, SettingManagerInterface $settingManager)
  {
    $this->settingManager = $settingManager;
    parent::__construct($registry);
  }

  public function createQuery($class, $alias = 'o')
  {
    $repository = $this->getEntityManager($class)->getRepository($class);

    return new ProxyQuery($repository->createQueryBuilder($alias), $this->settingManager, $this->getEntityManager($class));
  }
}