<?php

namespace Tests;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class FixtureAwareWebTestCase extends WebTestCase
{
  /**
   * @var ORMExecutor
   */
  private $fixtureExecutor;

  /**
   * @var ContainerAwareLoader
   */
  private $fixtureLoader;

  /**
   * @var EntityManager
   */
  private $entity_manager;

  /**
   * @var ORMPurger
   */
  private $purger;

  public static function setUpBeforeClass ()
  {
    self::bootKernel();
  }


  protected function setUp()
  {
    $this->entity_manager = self::$kernel->getContainer()->get('doctrine')->getManager();
    $this->purger = new ORMPurger($this->entity_manager);
    $this->purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
    $this->entity_manager->getConnection()->executeQuery('SET FOREIGN_KEY_CHECKS=0');
    $this->purger->purge();
    $this->purger->setPurgeMode(ORMPurger::PURGE_MODE_DELETE);
    $this->entity_manager->getConnection()->executeQuery('SET FOREIGN_KEY_CHECKS=1');
  }

  /**
   * Добавить фикстуру к имеющимся, без очищения старых данных
   * @param FixtureInterface $fixture
   * @param $append boolean Залить ли фикстуру повторно
   */
  protected function appendFixture(FixtureInterface $fixture, $append = false)
  {
    if ($append || !$this->getFixtureLoader()->hasFixture($fixture))
    {
      $this->getFixtureLoader()->addFixture($fixture);
      $this->getFixtureExecutor()->execute([$fixture], true);
    }
  }

  /**
   * Adds a new fixture to be loaded.
   *
   * @param FixtureInterface $fixture
   */
  protected function addFixture(FixtureInterface $fixture)
  {
    $this->getFixtureLoader()->addFixture($fixture);
  }

  /**
   * Executes all the fixtures that have been loaded so far.
   */
  protected function executeFixtures()
  {
    $this->getFixtureExecutor()->execute($this->getFixtureLoader()->getFixtures());
  }

  /**
   * @return ORMExecutor
   */
  private function getFixtureExecutor()
  {
    if (!$this->fixtureExecutor) {
      /** @var \Doctrine\ORM\EntityManager $entityManager */
      $entityManager = $this->getEntityManager();
      $this->fixtureExecutor = new ORMExecutor($entityManager, new ORMPurger($entityManager));
    }
    return $this->fixtureExecutor;
  }

  /**
   * @return ContainerAwareLoader
   */
  private function getFixtureLoader()
  {
    if (!$this->fixtureLoader) {
      $this->fixtureLoader = new ContainerAwareLoader(self::$kernel->getContainer());
    }
    return $this->fixtureLoader;
  }

  /**
   * @return EntityManager
   */
  protected function getEntityManager()
  {
    if (!$this->entity_manager)
    {
      $this->entity_manager = self::$kernel->getContainer()->get('doctrine')->getManager();
    }

    return $this->entity_manager;
  }

  /**
   * Получить объект по референсу из фикстур
   * @deprecated
   * @param $reference
   * @return object
   */
  protected function getByReference($reference)
  {
    return $this->getReference($reference);
  }

  /**
   * Получить объект по референсу из фикстур
   * @param $reference
   * @return object
   */
  protected function getReference($reference)
  {
    return $this->getFixtureExecutor()->getReferenceRepository()->getReference($reference);
  }
}