<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 17.06.2017
 * Time: 20:36
 */

namespace Accurateweb\SphinxSearchBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SearchdStartCommand extends SphinxSearchCommand
{
  protected function configure()
  {
    $this
      // the name of the command (the part after "bin/console")
      ->setName('sphinx:searchd:start')
      // the short description shown while running "php bin/console list"
      ->setDescription('Starts Sphinx Search daemon (searchd).')
      // the full command description shown when running the command with
      // the "--help" option
      ->setHelp("Starts Sphinx Search daemon (searchd)");
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $sphinxConfigPath = $this->getContainer()->get('accurateweb.sphinxsearch')->getSphinxConfigFilePath();

    $this->executeSphinxCommand($output, 'searchd', array('config' => '"' . $sphinxConfigPath . '"'));
  }
}