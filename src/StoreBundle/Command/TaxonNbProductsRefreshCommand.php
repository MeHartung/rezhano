<?php

namespace StoreBundle\Command;

use Doctrine\ORM\EntityManager;
use StoreBundle\Service\Taxon\TaxonNbProductsAggregate;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TaxonNbProductsRefreshCommand extends ContainerAwareCommand
{
  protected function configure ()
  {
    $this
      ->setName('taxon:nbProducts:refresh')
      ->setDescription('...');
  }

  protected function execute (InputInterface $input, OutputInterface $output)
  {
    /** @var EntityManager $em */
    $em = $this->getContainer()->get('doctrine.orm.entity_manager');
    $repository = $em->getRepository('StoreBundle:Store\Catalog\Taxonomy\Taxon');
    $taxons = $repository->findAll();
    $service = new TaxonNbProductsAggregate($em);

    foreach ($taxons as $taxon)
    {
      $service->recalculate($taxon);
    }

    $output->writeln('Command result.');
  }

}
