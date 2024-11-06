<?php

namespace Accurateweb\SettingBundle\Model;

use Accurateweb\SettingBundle\Model\Manager\SettingManagerInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use StoreBundle\Entity\Setting;

class ProxyQuery extends \Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery
{
  protected $settingManager;
  protected $entityManager;

  public function __construct (QueryBuilder $queryBuilder, SettingManagerInterface $settingManager, EntityManager $entityManager)
  {
    $this->settingManager = $settingManager;
    $this->entityManager = $entityManager;
    parent::__construct($queryBuilder);
  }

  public function execute(array $params = array(), $hydrationMode = null)
  {
    $result = parent::execute($params, $hydrationMode);

    $settings = $this->settingManager->getSettings();

    foreach ($result as $item)
    {
      /** @var Setting $item */
      $name = $item->getName();

      if (isset($settings[$name]))
      {
        unset($settings[$name]);
      }
    }

    foreach ($settings as $setting)
    {
      if($this->entityManager->find('StoreBundle:Setting', $setting->getName())) continue;
      
      $config = new Setting();
      $config->setName($setting->getName());
      $config->setValue($setting->getValue());
//      $config->setDescription($setting->getDescription());
      $result[] = $config;
      $this->entityManager->persist($config);
    }

    $this->entityManager->flush();


    return $result;
  }
}