<?php

namespace StoreBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use StoreBundle\Entity\Store\Catalog\Taxonomy\Taxon;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Gedmo\Tree\Strategy\ORM\Nested;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NestedSetRefreshCommand extends ContainerAwareCommand
{
  /**
   * {@inheritdoc}
   */
  protected function configure()
  {
    $this
      ->setName('vendor:nested-set:refresh')
      ->addArgument('entity', InputArgument::OPTIONAL, null, 'StoreBundle:Store\\Catalog\\Taxonomy\\Taxon');
  }

  /**
   * @param InputInterface $input
   * @param OutputInterface $output
   * @throws \Exception
   * @return int
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $logger = $this->getContainer()->get('logger');
    $container = $this->getContainer();
    /** @var EntityManagerInterface $em */
    $em = $container->get('doctrine.orm.entity_manager');
    $repository = $em->getRepository($input->getArgument('entity'));

    $nestedTree = new NestedTreeRepository($em, $em->getClassMetadata($repository->getClassName()));

      /** @var bool|array $verify */
       $verify = $nestedTree->verify();

       if (is_array($verify))
       {
         try
         {
           $errosStr = '';

           foreach ($verify as $error)
           {
             $errosStr = $errosStr . "\n" . $error;
           }

           $logger->addInfo($errosStr);

           $nestedTree->recover();
           $em->flush();

         } catch (\Exception $e)
         {
           $logger->addError($e->getMessage() . "\n" . $e->getTraceAsString());
         }

         $output->writeln("Tree successfully rebuilt");
         $output->writeln("List of fixed errors:");

         $tbl = new Table($output);
         $tbl->setHeaders(['№', 'Error']);
         $rows = [];

         foreach ($verify as $key => $item)
         {
           $rows[] = [$key, $item];
           $rows[] = new TableSeparator();
         }

         $tbl->setRows($rows);
         $tbl->render();
       }
       else
       {
         $logger->addInfo('No errors found');
         $output->writeln("No errors found");
       }
  }

  public function buildChildren(Taxon $parent, $treeLevelParent, $treeLeftParent, EntityManagerInterface $em)
  {
      $children = $em->getRepository(Taxon::class)->findBy(["parent" => $parent]);

      if($children)
      {
          foreach ($children as $child)
          {
              $rght = $this->buildChildren($child, $treeLevelParent, $treeLeftParent, $em);
              $child->setTreeRight($rght);
              $child->setTreeLevel($treeLevelParent);
              $child->setTreeLeft($treeLeftParent);

              $treeLeftParent = $rght+1;
              $rght++;

              $em->persist($child);
          }
      }
      else
      {
          # если детей нет
          $rght = $treeLeftParent+1;
      }

      return $rght;
  }
}