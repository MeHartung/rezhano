<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 26.03.18
 * Time: 14:29
 */

namespace StoreBundle\Unit\Command;


use Doctrine\ORM\Query\ResultSetMapping;
use StoreBundle\Command\NestedSetRefreshCommand;
use StoreBundle\DataFixtures\Taxon\InvalidTaxonFixtures;
use StoreBundle\Entity\Store\Catalog\Taxonomy\Taxon;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Symfony\Component\Console\Tester\CommandTester;
use Tests\StoreBundle\StoreWebTestCase;

class NestedSetRefreshCommandTest extends StoreWebTestCase
{

  public function setUp()
  {
    parent::setUp();
    $this->appendFixture(new InvalidTaxonFixtures());
  }

  public function testNestedTreeRefreshNotValidTree()
  {
    $rootSubTaxonSecondChild = $this->getByReference('second-invalid-3-lvl');
    /**
     * Теперь портим дерево - ставим TreeLeft у 'Ребенок 2 ребёнка' так,
     * чтобы он считался чужим ребёнком
     * Для этого вычитаем с его левого древа 2.
     */
    $sql = sprintf('UPDATE StoreBundle:Store\Catalog\Taxonomy\Taxon t SET t.treeLeft = %s WHERE t.id= %s',
      $rootSubTaxonSecondChild->getTreeLeft() - 2, $rootSubTaxonSecondChild->getId());
    $this->em->createQuery($sql)->getResult();

    $this->em->clear();

    $repository = $this->em->getRepository('StoreBundle:Store\Catalog\Taxonomy\Taxon');
    $root = $repository->getRootNode();
    $nodes = $repository->childrenQueryBuilder($root)->getQuery()->getArrayResult();
    $tree = $repository->buildTreeArray($nodes);

    /**
     * Проверим, что дерево поломано
     */
    foreach ($tree as $node)
    {
      if ($node['slug'] == "child-2")
      {
        $this->assertEquals(0, count($node['__children']));
      }
    }

    $output = $this->runCommand();
    /**
     * Т.к. выволдится табл. и неудобно парсить, просто проверим что там есть ожадаемые строки
     */
    $this->assertContains("Tree successfully rebuilt", $output);
    $this->assertContains("List of fixed errors:", $output);
    /**
     * Т.к. мы вычли 2 из левого дерева и вырезали других детей
     */
    $this->assertContains("duplicate on tree root: Каталог", $output);

    $this->em->clear();
    $nodes = $repository->childrenQueryBuilder($root)->getQuery()->getArrayResult();
    $tree = $repository->buildTreeArray($nodes);

    /**
     * Проверим, что дерево целое
     */
    foreach ($tree as $node)
    {
      if ($node['slug'] == "child-2")
      {
        $this->assertEquals(1, count($node['__children']));
      }
    }
  }

  public function testNestedTreeRefreshValidTree()
  {
    /**
     * Вызывем команду и проверяем, что она ничего делать не стала
     */
    $output = $this->runCommand();
    $this->assertSame('No errors found', trim($output));
  }

  public function runCommand()
  {
    $application = $this->application;

    $application->add(new NestedSetRefreshCommand());

    $command = $application->find('vendor:nested-set:refresh');
    $commandTester = new CommandTester($command);
    $commandTester->execute(array(
      'command' => $command->getName(),
    ));

    return $output = $commandTester->getDisplay();
  }

}