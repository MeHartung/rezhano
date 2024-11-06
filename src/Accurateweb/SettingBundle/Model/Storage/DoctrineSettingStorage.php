<?php

namespace Accurateweb\SettingBundle\Model\Storage;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class DoctrineSettingStorage implements SettingStorageInterface
{
  private $settingsRepository;
  private $em;
  private $settings;

  public function __construct (EntityRepository $settingsRepository, EntityManager $em)
  {
    $this->settings = [];
    $this->settingsRepository = $settingsRepository;
    $this->em = $em;
  }

  public function get ($name)
  {
    if (!isset($this->settings[$name]))
    {
      $this->settings[$name] = $this->settingsRepository->findOneBy(array('name' => $name));

      if (!$this->settings[$name])
      {
//        $class = $this->settingsRepository->getClassName();
//        $this->settings[$name] = new $class();
//        $this->settings[$name]->setName($name);
//        $this->em->persist($this->settings[$name]);
//        $this->em->flush();
        return null;
      }
    }

    return $this->settings[$name]->getValue();
  }

  public function set ($name, $value)
  {
    if (!isset($this->settings[$name]))
    {
      $this->settings[$name] = $this->settingsRepository->findOneBy(array('name' => $name));

      if (!$this->settings[$name])
      {
        $class = $this->settingsRepository->getClassName();
        $this->settings[$name] = new $class();
        $this->settings[$name]->setName($name);
      }
    }

    $this->settings[$name]->setValue($value);
    $this->em->persist($this->settings[$name]);
    $this->em->flush();
  }
}