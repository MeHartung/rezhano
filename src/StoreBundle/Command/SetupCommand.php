<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 11.09.2018
 * Time: 16:18
 */

namespace StoreBundle\Command;

use StoreBundle\Entity\Store\Catalog\Taxonomy\Taxon;
use StoreBundle\Exception\Catalog\RootNodeNotFoundException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SetupCommand extends ContainerAwareCommand
{
  protected function configure()
  {
    $this
      ->setName('store:setup')
      ->setDescription("Setup a new store");
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $em = $this->getContainer()->get('doctrine')->getManager();

    try
    {
      $em->getRepository('StoreBundle:Store\Catalog\Taxonomy\Taxon')->getRootNode();
    }
    catch (RootNodeNotFoundException $rootNodeNotFoundException)
    {
      $rootNode = new Taxon();
      $rootNode->setParent(null);
      $rootNode->setName('Каталог');
      $rootNode->setShortName('Каталог');
      $rootNode->setSlug('catalog');

      $em->persist($rootNode);
      $em->flush();
    }

  }
}